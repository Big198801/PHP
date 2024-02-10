<?php

namespace app\model;

use app\interfaces\IQueryBuider;
use app\interfaces\IRepository;

class QueryBuilder implements IQueryBuider
{

    protected const AGR_FUNCTIONS = ['AVG', 'COUNT', 'MAX', 'MIN', 'SUM'];
    protected const OPERATORS = ['<', '<=', '=', '>', '>=', '!='];

    /*Фильтры
      Один элемент этого массива имеет структуру - [
          field - поле
          operator - оператор сравнения
          value - значение для сравнения
          connector - 'OR' или 'AND' для соединения нескольких условий
      ]
    */
    protected $where = [];

    //Сортировка
    protected $order = [];

    //Таблицы, в будущем возможно реализуем работу с несколькими таблицами
    //Структура: ['tableName' => 'fields'] 

    protected $repo;
    protected $model;

    public function __construct(IRepository $repo) {
        $this->repo = $repo;
        $className = '\\' . $repo->getEntityClass();
        $this->model = new $className;
    }

    public function __call($method, $parameters)
    {
        $up_method = strtoupper($method);

        if (array_search($up_method, static::AGR_FUNCTIONS) !== false) {
            return $this->agrFunc($up_method, $parameters);
        }

        $connector = 'AND';

        switch ($method) {
            case 'orWhere': 
                $connector = 'OR';
            case 'where':
                return $this->whereFunc($parameters, $connector);
            case 'orderBy':
                return $this->orderFunc($parameters);             
            default:
                if (method_exists($this, $method)) {
                    return call_user_func_array([$this, $method], $parameters);    
                } else {
                    throw new \Exception("Метод {$method} не существует!");    
                };    
        }
    }

    protected function isCorrectField($field) {
        return $this->model->isProperties($field) || array_search($field, $this->repo->getSystemProps()) !== false; 
    }

    protected function whereFunc($parameters, $connector = 'AND') {
        $count_par = count($parameters);
        if ($count_par < 2 || $count_par > 3) {
            throw new \Exception("Недопустимое количество параметров метода where!");    
        }

        $field = $parameters[0];
        $operator = ($count_par == 2) ? '=' : $parameters[1];
        $value = $parameters[$count_par - 1];
        if (!($this->isCorrectField($field))) {
            throw new \Exception("Не существует поле {$field} для условной выборки!"); 
        }

        if (array_search($operator, static::OPERATORS) === false) {
            throw new \Exception("Не верный оператор '{$operator}' передан в качестве параметра №2!"); 
        }

        $this->where[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'connector' => $connector
        ];
        return $this;
    
    }

    protected function orderFunc($parameters) {
        foreach ($parameters as $field) {
            $field_words = explode(" ",$field);
            if (!$this->isCorrectField($field_words[0])) {
                throw new \Exception("Не существует поля {$field_words[0]} для условной выборки!"); 
            }
        }

        $old_order = $this->order;
        $this->order = array_merge($this->order, $this->getFieldsForQuery($parameters)); 

        return $this;

    }

    protected function agrFunc($method, $parameters) {
        
        $agr_params = $this->getFieldsForQuery($parameters);
        if (count($agr_params) == 0 && $method == 'COUNT') {
            $agr_params[] = '*';
        }

        if (count($agr_params) == 0) {
            throw new \Exception('У агрегационных методов должны быть параметры в качестве полей');
        } 


        $fields = array_map(function ($agr) use ($method) {
            return "{$method}({$agr})";
        }, $agr_params);


        $query = $this->getSQLAndParams($fields,[]);
        $result = $this->repo->getDb()->queryOne($query['sql'],$query['params']);
        return (count($result) == 1) ? $result[array_keys($result)[0]] : $result;
    }

    //Функция обратаывает массив полей для передачи SQL. Происходит проверка, есть ли такое поле, если есть обрамлляем в обратные кавычки. 
    //Также разрешено использовать вместе с именами полей DESC для сортировки и агрегатные функции
    protected function getFieldsForQuery($fields) {
        $result = [];

        foreach ($fields as $field) {

            $field_words = explode(" ", $field);
            if ($this->isCorrectField($field_words[0])) {
                $desc = (count($field_words) > 1 && $field_words[1]='DESC') ? " DESC" : "";
                $result[] = "`{$field_words[0]}`{$desc}";
                continue;
            }

            //Решил, что в полях fields разрешено использовать агрегирующие функции, поэтому проверим на них
            preg_match("/([a-z]+)\([\'\`]?([\*a-z]+)[\'\`]?\)/i", $field, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) == 3 && array_search($matches[1][0], static::AGR_FUNCTIONS) !== false) {
                if ($this->isCorrectField($matches[2][0])) {
                    $result[] = "{$matches[1][0]}(`{$matches[2][0]}`) AS `{$matches[2][0]}`";    
                } elseif ($matches[2][0]=="*")  {
                    $result[] = "{$matches[1][0]}(*) AS {$matches[1][0]}";
                }
            }
        }

        return $result;
    }

    protected function _getWhereStrAndParams($where, $index = 0) {    
        $where_str = "";
        $query_params = [];
        foreach ($where as $where_one) {
            if (array_search($where_one['operator'], static::OPERATORS) === false || !$this->isCorrectField($where_one['field'])) {
                continue;
            }
            $param_name = "W_{$index}";
            $where_str .= sprintf(" %s `{$where_one['field']}` {$where_one['operator']} :{$param_name}",
                ($where_str == "") ? "" : (($where_one['connector'] == "OR") ? "OR" : "AND")
            );
            $query_params[$param_name] = $where_one["value"];
            $index++;
        }
        return ['str' => $where_str, 'params' => $query_params];
    }

    public function getWhereStrAndParams($where) {    
        $where_left = $this->_getWhereStrAndParams($where);
        $where_right = $this->_getWhereStrAndParams($this->where, count($where_left['params']));
        $query_params = array_merge($where_left['params'], $where_right['params']);
        
        if ($where_left['str'] === "" || $where_right['str'] === "") {
            $where_str = $where_left['str'] . $where_right['str'];
        } else {
            $where_str = "({$where_left['str']}) AND ({$where_right['str']})";
        }
        $where_str = ($where_str == "") ? "" : " WHERE {$where_str}";
        return ['str' => $where_str, 'params' => $query_params];
    }

    //Формируем динамический запрос, возвращается массив из двух элементов ['sql' - текст запроса, 'params' - параметры]
    public function getSQLAndParams($fields = [], $where = [], $limit = 0, $offset = 0) {
        $fields = $this->getFieldsForQuery($fields);
        $order = $this->getFieldsForQuery($this->order);

        $whereStrAndParams = $this->getWhereStrAndParams($where);

        $fields_query = ($fields) ? implode(", ", $fields) : "*";
        $order_by = ($this->order) ? (" ORDER BY " . implode(", ", $this->order)) : "";
        $limit = ($limit > 0) ? (" LIMIT " . (int)$offset . ", " . (int)$limit)  : "";
        
        return 
        [
            "sql" => "SELECT {$fields_query} FROM {$this->repo->getTableName()} {$whereStrAndParams['str']} {$order_by} {$limit}",
            "params" => $whereStrAndParams['params']
        ];
    }

    public function first()
    {
        $query = $this->getSQLAndParams([], [], 1, 0);
        return $this->repo->getDb()->queryObject($query['sql'], $query['params'], $this->repo->getEntityClass());
    }

    public function find($id)
    {
        $whereId = ['field' => $this->model->getKeyFieldName(), 'operator' => '=', 'value' => $id];
        $query = $this->getSQLAndParams([], [$whereId], 1, 0);
        return $this->repo->getDb()->queryObject($query['sql'], $query['params'], $this->repo->getEntityClass());        
    }

    public function get($limit=0, $offset=0)
    {
        $query = $this->getSQLAndParams([], [], $limit, $offset);
        return $this->repo->getDb()->queryObjects($query['sql'],$query['params'], $this->repo->getEntityClass());
    }

}
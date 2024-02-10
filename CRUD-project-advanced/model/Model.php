<?php

namespace app\model;

use app\engine\Db;

abstract class Model
{
    protected $keyFieldName = 'id';    
    protected $props = [];
    protected $protectedProps = [];

    // Через это свойство реализуем связь один-к-одному с другими моделями
    //  ['model' => ['fieldName', 'className', 'instance']] 
    protected $realatedModels = [];

    public function __set($name, $value) {
        if (
            array_key_exists($name, $this->props) && 
            $value != $this->$name && 
            array_search($name, $this->protectedProps) === false
            ) {
            
            $this->clearInstanceInRM($name);
            $this->props[$name] = true;
            $this->$name = $value;
        }    
    }

    public function __get($name)
    {
        //Код для работы со связанными таблицами, например в модели CartItem мы сможем обращатться к продукту как product.name
        if (array_key_exists($name, $this->realatedModels) && $this->realatedModels[$name]['fieldName']) {
            if (!isset($this->realatedModels[$name]['instance'])) {
                $className = ($this->realatedModels[$name]['className']) ?: $name;
                if (strpos($className,'\\') === false) {
                    $className = MODEL_NAMESPACE . $className;
                }
                if (class_exists($className)) {
                    $fn = $this->realatedModels[$name]['fieldName'];
                    $this->realatedModels[$name]['instance'] = $className::find($this->$fn);
                }
            }
        
            return $this->realatedModels[$name]['instance'];
        };

        if ($this->isProperties($name) || $name='props') {
            return $this->$name;
        }              
    }

    public function setKeyValue($value) {
        if (empty($this->getKeyValue())) {
            $id = $this->getKeyFieldName();
            $this->$id = $value;
        }
    }

    public function __isset($name)
    {
        return (array_key_exists($name, $this->realatedModels)) ?: $this->isProperties($name);     
    }

    protected function clearInstanceInRM($fieldName) {
        foreach ($this->realatedModels as $key => $value) {
            if ($value['fieldName'] == $fieldName) {
                unset($value['instance']); 
                break;
            }
        }
    }

    public function clearProps() {
        foreach ($this->props as $key=>$value) {
            $this->props[$key] = false;
        }
    }
 
   public function getDataFields($fields=[]) {
       $result = [];
       foreach ($this->getFields() as $field) {
           if (count($fields) !== 0 && array_search($field, $fields) === false) continue; 
           $result[$field] = $this->$field;
        }

        foreach (array_keys($this->realatedModels) as $field) {
            if (count($fields) !== 0 || array_search($field, $fields) !== false) continue; 
            if ($this->$field) {
                $result[$field] = $this->$field->getDataFields();
            }
        }

        return $result;
    }

    public function getFields() {
        return array_merge([$this->keyFieldName], array_keys($this->props));
    }   

    public function getKeyFieldName() {
        return $this->keyFieldName;
    }

    public function getKeyValue() {
        $id = $this->getKeyFieldName();
        return $this->$id;
    }    

    public function isProperties($name) {
        return ($name == $this->keyFieldName || key_exists($name, $this->props)); 
   }  

}
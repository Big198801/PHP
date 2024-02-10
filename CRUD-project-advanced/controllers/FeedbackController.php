<?php

namespace app\controllers;

use app\model\entities\Feedback;

class FeedbackController extends Controller
{

    public function actionIndex() {
        echo $this->render('feedback', ['page_size' => \App::getConfig('pageSize')]);
    }

    public function actionApi($params) {
        $result = [];
        if (isset($params['categoryFeedback'])) {
            \Feedback::setCategoty($params['categoryFeedback']);
        }
        $query = \Feedback::orderBy('id DESC');

        if (\Feedback::getGroupFieldName()) {
            \Feedback::setSystemProp(\Feedback::getGroupFieldName(), $params['groupId']);
            $query = $query->where(\Feedback::getGroupFieldName(), $params['groupId']);
        }

        if (isset($params['id'])) {
            $feedback = $query->find($params['id']);
        }

        switch ($params['action']) {
            case 'getItems': 
                echo $this->getJSONDynamicList($query, $params);
                return;

            case 'delete': 
                if ($feedback && \Feedback::delete($feedback)) {
                    $result['result'] = 'ok';
                } else {
                    $result['error'] = 'error';
                }
                break;

            case 'add': 
                $feedback = new Feedback($params['name'], $params['feedback']);
            case 'edit': 
                $result['error'] = 'error';
                if ($feedback && $params['name'] && $params['feedback']) { 
                    $feedback->name = $params['name'];
                    $feedback->feedback = $params['feedback'];
                    if (\Feedback::save($feedback) && $feedback->getKeyValue()) {
                        $result = ['result' => 'ok'];
                    }
                }
                break;

            default: 
                $result['error'] = 'Не существующий метод';
        }

        if (empty($result['error']) && $feedback->getKeyValue()) {
            $result = [
                'result' => 'ok',
                'id' => $feedback->getKeyValue(),
                'name' => $feedback->name,
                'feedback' => $feedback->feedback
            ];
        }

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}
<?php

namespace app\model\repositories;

use app\model\Repository;
use app\model\entities\Feedback;

class FeedbackRepository extends Repository
{
    
    protected $category = "";

    protected const CATEGORIES = [
        'product' => 'product_id'
    ];

    public function getEntityClass()
    {
        return Feedback::class;
    }

    public function getTableName()
    {
        return "feedback" . ($this->category ? ("_".$this->category) : "");
    }

    public function setCategoty($category) {
        if (array_key_exists($category, static::CATEGORIES)) {
            $this->category = $category;
            $this->systemProps = [static::CATEGORIES[$category]];
        }
    }


    public function getGroupFieldName() {
        return static::CATEGORIES[$this->category];
    }


    
}

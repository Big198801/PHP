<?php

namespace app\model\entities;

use app\model\Model;

class Feedback extends Model
{
    protected $id;
    protected $name;
    protected $feedback;   

    protected $props = [
            'name' => false,
            'feedback' => false,
    ];

    public function __construct($name = null, $feedback = null)
    {
        $this->name = $name;
        $this->feedback = $feedback;
    }

}

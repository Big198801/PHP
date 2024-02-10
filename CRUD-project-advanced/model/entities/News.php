<?php

namespace app\model\entities;

use app\model\Model;

class News extends Model
{
    protected $id;
    protected $title;
    protected $text;   

    protected $props = [
            'title' => false,
            'text' => false,
    ];


    public function __construct($title = null, $text = null)
    {
        $this->title = $title;
        $this->text = $text;
    }

}

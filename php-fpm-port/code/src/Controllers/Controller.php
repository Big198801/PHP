<?php

namespace Myproject\Application\Controllers;

use Myproject\Application\Render;

class Controller
{
    protected Render $render;

  public function __construct()
    {
        $this->render = new Render();
    }
}
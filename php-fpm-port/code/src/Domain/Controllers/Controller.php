<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Application\Render;

class Controller
{
    protected Render $render;

  public function __construct()
    {
        $this->render = new Render();
    }
}
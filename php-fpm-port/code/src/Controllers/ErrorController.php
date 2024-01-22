<?php

namespace Myproject\Application\Controllers;

use Myproject\Application\Render;
use Myproject\Application\Models\User;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ErrorController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function error404(): string
    {
        $render = new Render();
        return $render->renderPage('404.tpl', ['title' => '404']);
    }
}
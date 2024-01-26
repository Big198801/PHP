<?php

namespace Myproject\Application\Controllers;
use Myproject\Application\Render;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PageController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function actionIndex(): string
    {
        $render = new Render();
        return $render->renderPage('page-index.tpl', ['title' => 'Главная страница']);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionError(): string
    {
        $render = new Render();
        return $render->renderPage('page-error.tpl', ['title' => '404']);
    }
}
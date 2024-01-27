<?php

namespace Myproject\Application\Controllers;
use Myproject\Application\Render;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PageController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function actionIndex(): string
    {
        return $this->render->renderPage('page-index.twig', ['title' => 'Главная страница']);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionError(): string
    {
        return $this->render->renderPage('page-error.twig', ['title' => '404']);
    }
}
<?php

namespace Myproject\Application\Domain\Controllers;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PageController extends Controller
{
    protected array $actionsPermissions = [
        'actionIndex' => ['admin', 'user'],
        'actionError' => ['admin', 'user'],
        'actionTime' => ['admin', 'user'],
    ];

    public function actionIndex(): string
    {
        return $this->render->renderPage('page-index.twig',
            ['title' => 'Главная страница']);

    }

    public function actionError(): string
    {
        return $this->render->renderPage('page-error.twig', ['title' => '404']);
    }

    public function actionTime(): string
    {
        return json_encode(date('d-m-Y H:i:s'));
    }
}
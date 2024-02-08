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
    ];

    public function actionIndex(): string
    {
        if (isset($_GET['error']) && $_GET['error']) {
            return $this->render->renderPage('page-index.twig',
                ['title' => 'Главная страница',
                    'alert_message' => $_SESSION['error_message'],
                    'alert_head' => 'Ошибка',
                    'alert' => true]);
        } else {
            return $this->render->renderPage('page-index.twig',
                ['title' => 'Главная страница']);
        }
    }

    public function actionError(): string
    {
        return $this->render->renderPage('page-error.twig', ['title' => '404']);
    }
}
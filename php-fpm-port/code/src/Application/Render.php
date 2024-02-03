<?php

namespace Myproject\Application\Application;

use Random\RandomException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Render
{
    private string $viewFolder = '/src/Domain/Views';
    private FilesystemLoader $loader;
    private Environment $environment;

    public function __construct()
    {
        $this->loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . $this->viewFolder);
        $this->environment = new Environment($this->loader, [
            // 'cache' => $_SERVER['DOCUMENT_ROOT'] . '/cache/',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws RandomException
     */
    public function renderPage(string $contentTemplateName = 'page-index.twig', array $templateVariables = []): string
    {
        $template = $this->environment->load($contentTemplateName);

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $templateVariables['csrf_token'] =  $_SESSION['csrf_token'];

        $templateVariables['time'] = date('d-m-Y H:i');

        return $template->render($templateVariables);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function renderExceptionPage(string $error) : string
    {
        $template = $this->environment->load('page-index.twig');

        $templateVariables['time'] = date('d-m-Y H:i');
        $templateVariables['alert_message'] = $error;
        $templateVariables['alert_head'] = 'Ошибка';
        $templateVariables['alert'] = true;

        return $template->render($templateVariables);
    }
}

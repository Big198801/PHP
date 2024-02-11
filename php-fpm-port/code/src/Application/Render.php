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

    public function renderPage(string $contentTemplateName = 'page-index.twig', array $templateVariables = []): string
    {
        $template = $this->environment->load($contentTemplateName);

        if (isset($_SESSION['user_name'])) {
            $templateVariables['user_authorized'] = true;
            $templateVariables['user_name'] = $_SESSION['user_name'];
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $templateVariables['csrf_token'] = $_SESSION['csrf_token'];
        $templateVariables['metrik'] = $_COOKIE['metrik'];

        $templateVariables['time'] = date('d-m-Y H:i');

        /* временный код
        ob_start();
        \xdebug_info();
        $xdebug = ob_get_clean();
        $templateVariables['xdebug'] = $xdebug;
        */

        return $template->render($templateVariables);
    }

    public function renderPageWithForm(array $templateVariables = []): string
    {
        $template = $this->environment->load('user-login.twig');
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $templateVariables['csrf_token'] = $_SESSION['csrf_token'];;

        return $template->render($templateVariables);
    }
}

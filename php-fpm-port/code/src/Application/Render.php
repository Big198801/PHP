<?php

namespace Myproject\Application\Application;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Render
{
    private string $viewFolder = '/src/Domain/Views';
    private FilesystemLoader $loader;
    private Environment $environment;

    public function __construct()
    {
        $this->loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/../' . $this->viewFolder);
        $this->environment = new Environment($this->loader, [
            // 'cache' => $_SERVER['DOCUMENT_ROOT'] . '/../' . '/cache/',
        ]);
    }

    public function renderPage(string $contentTemplateName = 'page-index.twig', array $templateVariables = []): string
    {
        $template = $this->environment->load($contentTemplateName);

        if (isset($_SESSION['auth']['user_name'])) {
            $templateVariables['user_authorized'] = true;
            $templateVariables['user_name'] = $_SESSION['auth']['user_name'];
            $templateVariables['user_lastname'] = $_SESSION['auth']['user_lastname'];
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $templateVariables['csrf_token'] = $_SESSION['csrf_token'];
        $templateVariables['metrik'] = $_COOKIE['metrik'];

        if (isset($_SESSION['auth']['hasAccess'])) {
            $templateVariables['hasAdmin'] = in_array('admin', $_SESSION['auth']['hasAccess'], true);
        }

        $templateVariables['time'] = date('d-m-Y H:i:s');

        if (isset($_GET['error']) && $_GET['error']) {
            $templateVariables['alert_message'] = $_SESSION['error_message'];
            $templateVariables['alert_head'] = 'Ошибка';
            $templateVariables['alert'] = true;
        }

        return $template->render($templateVariables);
    }

    public function renderPageWithForm(array $templateVariables = []): string
    {
        $template = $this->environment->load('page-login.twig');
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $templateVariables['csrf_token'] = $_SESSION['csrf_token'];;

        return $template->render($templateVariables);
    }
}

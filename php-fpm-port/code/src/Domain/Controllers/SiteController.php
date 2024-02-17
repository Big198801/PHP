<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Domain\Models\SiteInfo;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SiteController extends Controller
{
    protected array $actionsPermissions = [
        'actionInfo' => ['admin', 'user']
    ];

    public function actionInfo(): string
    {
        $info = new SiteInfo();
        return $this->render->renderPage("page-info.twig", [
            'title' => 'Информация',
            'server' => $info->getWebServer(),
            'phpVersion' => $info->getPhpVersion(),
            'userAgent' => $info->getUserAgent()
        ]);
    }
}
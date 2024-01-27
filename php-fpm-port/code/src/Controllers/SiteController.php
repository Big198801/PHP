<?php

namespace Myproject\Application\Controllers;

use Myproject\Application\Models\SiteInfo;
use Myproject\Application\Render;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SiteController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function actionInfo(): string
    {
        $info = new SiteInfo();
        return $this->render->renderPage("site-info.twig", [
            'title' => 'Информация',
            'server' => $info->getWebServer(),
            'phpVersion' => $info->getPhpVersion(),
            'userAgent' => $info->getUserAgent()
        ]);
    }
}
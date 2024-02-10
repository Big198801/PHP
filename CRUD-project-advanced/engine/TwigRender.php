<?php

namespace app\engine;

use app\interfaces\IRenderer;

class TwigRender implements IRenderer
{

    public function __construct()
    {
         $loader = new \Twig\Loader\FilesystemLoader(\App::getConfig('templates_dir'));
         $this->twig = new \Twig\Environment($loader);

    }    

    public function renderTemplate($template, $params = []) {
        return $this->twig->render($template . '.twig', $params);
    }
}
<?php

namespace app\engine;

use app\interfaces\IRender;

class TwigRender implements IRender
{
    protected $twig;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($loader);
    }

    public function renderTemplate($template, $params = [])
    {

        return $this->twig->render($template . '.twig', $params);
    }
}
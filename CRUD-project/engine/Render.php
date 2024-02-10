<?php

namespace app\engine;

use app\interfaces\IRender;

class Render implements IRender
{
    public function renderTemplate($template, $params = [])
    {
        ob_start();
        extract($params);
        include App::call()->config['views_dir'] . $template . '.php';
        return ob_get_clean();
    }
}
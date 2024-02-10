<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Application\Render;
use Myproject\Application\Domain\Models\UserRolesTrait;
use Myproject\Application\Domain\Models\Validate;

abstract class Controller
{
    protected Render $render;

    protected Validate $validate;
    protected array $actionsPermissions = [];

    public function __construct()
    {
        $this->render = new Render();
        $this->validate = new Validate();
        if (!isset($_COOKIE['metrik'])) {
            $_COOKIE['metrik'] = 0;
        }
        $_COOKIE['metrik']++;

        setcookie('metrik', $_COOKIE['metrik'], time() + 3600 * 24 * 7, '/');
    }

    use UserRolesTrait;

    public function getActionsPermissions(string $methodName): array
    {
        return $this->actionsPermissions[$methodName] ?? [];
    }
}
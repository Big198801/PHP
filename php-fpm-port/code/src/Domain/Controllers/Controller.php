<?php

namespace Myproject\Application\Domain\Controllers;

use Myproject\Application\Application\Render;
use Myproject\Application\Infrastructure\Storage;

class Controller
{
    protected Render $render;
    protected array $actionsPermissions = [];

    public function __construct()
    {
        $this->render = new Render();
    }

    public function getUserRoles(): array
    {
        $roles = ['user'];

        if (isset($_SESSION['id_user'])) {
            $rolesSql = "SELECT * FROM user_roles WHERE id_user = :id";

            $handler = Storage::get()->prepare($rolesSql);
            $handler->execute(['id' => $_SESSION['id_user']]);
            $result = $handler->fetchAll();

            if (!empty($result)) {
                foreach ($result as $role) {
                    $roles[] = $role['role'];
                }
            }
        }
        return $roles;
    }

    public function getActionsPermissions(string $methodName): array
    {
        return $this->actionsPermissions[$methodName] ?? [];
    }
}
<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateShopTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('products')
            ->addColumn('name', 'text')
            ->addColumn('description', 'text')
            ->addColumn('price', 'float')
            ->create();

        $this->table('basket')
            ->addColumn('session_id', 'text')
            ->addColumn('product_id', 'integer')
            ->create();

        $this->table('users')
            ->addColumn('login', 'text')
            ->addColumn('pass', 'text')
            ->create();
    }
}

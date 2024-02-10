<?php

App::bind('auth', 'app\\engine\\Auth', true);
App::bind('render', 'app\\engine\\Render', true);
App::bind('router', 'app\\engine\\Router', true);
App::bind('request', 'app\\engine\\Request', true);
App::bind('session', 'app\\engine\\Session', true);
App::bind(\app\interfaces\IRenderer::class, 'app\\engine\\TwigRender'); 
App::bind('news', 'app\\model\\repositories\\NewsRepository', true);
App::bind('products', 'app\\model\\repositories\\ProductsRepository', true);
App::bind('users', 'app\\model\\repositories\\UsersRepository', true);
App::bind('feedback', 'app\\model\\repositories\\FeedbackRepository', true);
App::bind('cart', 'app\\model\\repositories\\CartRepository', true);
App::bind('orders', 'app\\model\\repositories\\OrderRepository', true);
App::bind('order_items', 'app\\model\\repositories\\OrderItemsRepository', true);
App::bind(\app\engine\Db::class, 'db');
App::bind('db', function ($app) {
    $config = $app->getConfig('db');
    return new \app\engine\Db(
        $config['driver'], 
        $config['host'], 
        $config['login'], 
        $config['password'], 
        $config['database'],
        $config['charset']
    );
});

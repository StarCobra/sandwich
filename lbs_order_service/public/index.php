<?php

declare(strict_types=1);

use lbs\order\errors\renderer\JsonErrorRenderer;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$config = parse_ini_file("../conf/order.db.conf.ini.dist");

$db = new \Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$app = AppFactory::create();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorMiddleware->getDefaultErrorHandler()->registerErrorRenderer('application/json', JsonErrorRenderer::class);
$errorMiddleware->getDefaultErrorHandler()->forceContentType('application/json');


/**  configuring API Routes **/

$app->get('/orders', \lbs\order\actions\GetOrders::class)->setName('orders');
$app->get('/order/{id}', \lbs\order\actions\GetOrderById::class)->setName('orderById');
$app->get('/order/{id}/items', \lbs\order\actions\GetOrderItems::class)->setName('orderItems');

$app->put('/order/{id}', \lbs\order\actions\UpdateOrderAction::class)->setName('updateOrder');


$app->run();

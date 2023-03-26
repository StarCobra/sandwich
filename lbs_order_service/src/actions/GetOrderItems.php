<?php

namespace lbs\order\actions;

use lbs\order\services\utils\OrderService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpMethodNotAllowedException;

final class GetOrderItems
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
            $rq->getMethod() != "GET" ?? throw new HttpMethodNotAllowedException($rq, "Methode non autorisée");

        try {
            $items = OrderService::getOrderItems($args["id"]);
            $data = [
                'type' => 'collection',
                'count' => count($items),
                'items' => $items
            ];

            $rs = $rs->withStatus($rs->getStatusCode())->withHeader('Content-Type', 'application/json');
            $rs->getBody()->write(json_encode($data));
            return $rs;
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
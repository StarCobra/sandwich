<?php

namespace lbs\order\actions;

use lbs\order\services\utils\OrderService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Routing\RouteContext;

final class GetOrderById
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
            $rq->getMethod() != "GET" ?? throw new HttpMethodNotAllowedException($rq, "Methode non autorisée");
        $embed = $rq->getQueryParams()["embed"] ?? null;

        try {
            $order = OrderService::getOrderById($args["id"], $embed);
            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

            $data = [
                'type' => 'resource',
                'order' => $order,
                'links' => [
                    'items' => [
                        'href' => $routeParser->urlFor("orderItems", ["id" => $args["id"]]),
                    ],
                    'self' => [
                        'href' => $routeParser->urlFor("orderById", ["id" => $args["id"]]),
                    ],
                ],
            ];

            $rs = $rs->withStatus($rs->getStatusCode())->withHeader('Content-Type', 'application/json');
            $rs->getBody()->write(json_encode($data));
            return $rs;
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}

<?php

namespace lbs\order\actions;

use lbs\order\services\utils\OrderService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Routing\RouteContext;

final class GetOrders
{
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
            $rq->getMethod() != "GET" ?? throw new HttpMethodNotAllowedException($rq, "Methode non autorisÃ©e");
        $name = $rq->getQueryParams()["c"] ?? null;
        $sort = $rq->getQueryParams()["sort"] ?? null;
        $page = $rq->getQueryParams()["page"] ?? null;
        $size = 10;

        try {

            $orders = OrderService::getOrders($name, $sort, $page, $size);
            $ordersWithoutPage = OrderService::getOrdersWithoutPage();

            $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

            if ($page == null) {
                $data = [
                    'type' => 'collection',
                    'count' => count($orders),
                    'orders' => [],
                ];
            } else {
                $data = [
                    'type' => 'collection',
                    'count' => count($ordersWithoutPage),
                    'size' => $size,
                    'orders' => [],
                ];
            }

            foreach ($orders as $key => $byOrder) {
                $data["orders"][$key]["order"] = $byOrder;
                $data["orders"][$key]["order"]["links"]["self"]["href"] = $routeParser->urlFor("orderById", ["id" => $byOrder["id"]]);
            }

            if (!($page == null)) {
                if ($page <= 0) {
                    $page = 1;
                    $prev = 1;
                }

                if ($page >= ceil(count($ordersWithoutPage) / $size)) {
                    $next = ceil(count($ordersWithoutPage) / $size);
                    $prev = (ceil(count($ordersWithoutPage) / $size) - 1);
                } else {
                    $next = $page + 1;
                    if ($page <= 1) {
                        $prev = 1;
                    } else {
                        $prev = $page - 1;
                    }
                }

                foreach ($orders as $key => $byOrder) {
                    $data["links"]["next"]["href"] = $routeParser->urlFor("orders") . "?page=" . $next;
                    $data["links"]["prev"]["href"] = $routeParser->urlFor("orders") . "?page=" . $prev;
                    $data["links"]["first"]["href"] = $routeParser->urlFor("orders") . "?page=1";
                    $data["links"]["last"]["href"] = $routeParser->urlFor("orders") . "?page=" . ceil(count($ordersWithoutPage) / $size);
                }
            }


            $rs = $rs->withStatus($rs->getStatusCode())->withHeader('Content-Type', 'application/json');
            $rs->getBody()->write(json_encode($data));
            return $rs;
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
}
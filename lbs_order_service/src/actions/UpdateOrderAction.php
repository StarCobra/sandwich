<?php

namespace lbs\order\actions;

use lbs\order\errors\exceptions\HttpNoContentException;
use lbs\order\services\utils\OrderService;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

final class UpdateOrderAction
{
    public function __invoke(Request $rq, Response $rs, mixed $args): void
    {
        $rq->getMethod() != "PUT" ?? throw new HttpMethodNotAllowedException($rq, "Methode non autorisée");
        $data = $rq->getParsedBody() ?? throw new HttpNotFoundException($rq, "Ressource non disponible : " . $rq->getUri()->getPath());

        if (isset($data["client_nom"]) && isset($data["client_mail"]) && isset($data["order_date"]) && isset($data["delivery_date"])) {
            if (OrderService::update($args["id"], $data)) {
                throw new HttpNoContentException($rq);
            } else {
                throw new HttpInternalServerErrorException($rq, "La ressource n'a pû être enregistée");
            }
        } else {
            throw new HttpInternalServerErrorException($rq, "La ressource n'a pû être enregistée");
        }
    }
}

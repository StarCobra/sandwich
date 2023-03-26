<?php

namespace lbs\order\errors\renderer;

use Slim\Interfaces\ErrorRendererInterface;

class JsonErrorRenderer implements ErrorRendererInterface
{

    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        $data = [
            'type' => 'error',
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}

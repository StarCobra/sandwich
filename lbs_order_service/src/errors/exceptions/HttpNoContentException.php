<?php

namespace lbs\order\errors\exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpNoContentException extends HttpSpecializedException
{
    protected $code = 204;
    protected $message = '';
    protected string $title = '204 No Content';
    protected string $description = 'La requête a réussi mais vous n\'avez pas besoin de quitter la page.';
}
<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class MailTypeNotFoundException extends Exception
{
    protected $message = 'MailType doesn\'t exist';

    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}

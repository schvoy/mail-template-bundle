<?php

/**
 * This file is part of the EightMarq Symfony bundles.
 *
 * (c) Norbert Schvoy <norbert.schvoy@eightmarq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EightMarq\MailTemplateBundle\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class MailTypeNotFoundException extends Exception
{
    protected $message = 'Mail type doesn\'t exist';

    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}
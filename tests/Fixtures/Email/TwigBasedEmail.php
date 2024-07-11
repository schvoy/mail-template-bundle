<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Fixtures\Email;

use Schvoy\MailTemplateBundle\Mailer\AbstractMailType;
use Schvoy\MailTemplateBundle\Mailer\Engine\TwigBased;

class TwigBasedEmail extends AbstractMailType
{
    use TwigBased;

    protected string $subject = 'test_email.subject';

    protected string $body = 'test_email.body';
}

<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Fixtures\Email;

use Schvoy\MailTemplateBundle\Mailer\AbstractMailType;
use Schvoy\MailTemplateBundle\Mailer\Engine\DoctrineBased;

class DoctrineBasedEmail extends AbstractMailType
{
    use DoctrineBased;

    protected string $key = 'test_email';
}

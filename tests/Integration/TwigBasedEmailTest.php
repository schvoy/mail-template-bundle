<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Integration;

use Schvoy\MailTemplateBundle\Mailer\MailSender;
use Schvoy\MailTemplateBundle\Mailer\Recipient;
use Schvoy\MailTemplateBundle\Tests\AbstractTestCase;
use Schvoy\MailTemplateBundle\Tests\Fixtures\Email\TwigBasedEmail;

class TwigBasedEmailTest extends AbstractTestCase
{
    public function testTwigBasedEmail(): void
    {
        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $twigBasedEmail = $mailSender->getMailType(TwigBasedEmail::class);

        $mailSender->send($twigBasedEmail, [new Recipient('recipient@example.com', 'Test user')]);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $body = $email->getHtmlBody();
        $from = $email->getFrom()[0];
        $to = $email->getTo()[0];

        $this->assertEquals('test@example.com', $from->getAddress());
        $this->assertEquals('Mail sender name', $from->getName());
        $this->assertEquals('recipient@example.com', $to->getAddress());
        $this->assertEquals('Test user', $to->getName());
        $this->assertEquals('This is a test email', $email->getSubject());
        $this->assertStringContainsString('<title>This is a test email</title>', $body);
        $this->assertStringContainsString('<h1>Welcome Test user!</h1>', $body);
        $this->assertStringContainsString('<p>Regards: Mail signatory</p>', $body);
    }

    protected function getEntityClass(): string|false
    {
        return false;
    }
}

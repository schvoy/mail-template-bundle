<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Integration;

use Exception;
use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Schvoy\MailTemplateBundle\Mailer\MailSender;
use Schvoy\MailTemplateBundle\Mailer\Recipient;
use Schvoy\MailTemplateBundle\Tests\AbstractTestCase;
use Schvoy\MailTemplateBundle\Tests\Fixtures\Email\TwigBasedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class TwigBasedEmailTest extends AbstractTestCase
{
    public function testTwigBasedEmailWithoutConfiguration(): void
    {
        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $twigBasedEmail = $mailSender->getMailType(TwigBasedEmail::class);

        $mailSender->send($twigBasedEmail, [
            new Recipient('recipient@example.com', 'Test user'),
            new Recipient('cc@example.com', 'Test CC user', cc: true),
            new Recipient('bcc@example.com', 'Test BCC user', bcc: true),
        ]);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $body = $email->getHtmlBody();
        $from = $email->getFrom()[0];
        $to = $email->getTo()[0];
        $cc = $email->getCc()[0];
        $bcc = $email->getBcc()[0];

        $this->assertEquals('test@example.com', $from->getAddress());
        $this->assertEquals('Mail sender name', $from->getName());
        $this->assertEquals('recipient@example.com', $to->getAddress());
        $this->assertEquals('Test user', $to->getName());
        $this->assertEquals('Test CC user', $cc->getName());
        $this->assertEquals('Test BCC user', $bcc->getName());
        $this->assertEquals('This is a test email', $email->getSubject());
        $this->assertStringContainsString('<title>This is a test email</title>', $body);
        $this->assertStringContainsString('<h1>Dear Test user,</h1>', $body);
        $this->assertStringContainsString('Content of the test email.', $body);
        $this->assertStringContainsString('Regards: Mail signatory', $body);
    }

    public function testTwigBasedEmailExtendEmailDuringBuild(): void
    {
        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $twigBasedEmail = $mailSender->getMailType(TwigBasedEmail::class);

        $mailSender->send(
            $twigBasedEmail,
            [
                new Recipient('recipient@example.com', 'Test user'),
            ],
            extendEmail: function (Email $email) {
                $email->addCc(new Address('cc@example.com', 'Test CC user'));
                $email->addBcc(new Address('bcc@example.com', 'Test BCC user'));
            }
        );

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $body = $email->getHtmlBody();
        $from = $email->getFrom()[0];
        $to = $email->getTo()[0];
        $cc = $email->getCc()[0];
        $bcc = $email->getBcc()[0];

        $this->assertEquals('test@example.com', $from->getAddress());
        $this->assertEquals('Mail sender name', $from->getName());
        $this->assertEquals('recipient@example.com', $to->getAddress());
        $this->assertEquals('Test user', $to->getName());
        $this->assertEquals('Test CC user', $cc->getName());
        $this->assertEquals('Test BCC user', $bcc->getName());
        $this->assertEquals('This is a test email', $email->getSubject());
        $this->assertStringContainsString('<title>This is a test email</title>', $body);
        $this->assertStringContainsString('<h1>Dear Test user,</h1>', $body);
        $this->assertStringContainsString('Content of the test email.', $body);
        $this->assertStringContainsString('Regards: Mail signatory', $body);
    }

    public function testTwigBasedEmailExtendConfigurationDuringBuild(): void
    {
        $mailSender = self::getContainer()->get(MailSender::class);

        $twigBasedEmail = $mailSender->getMailType(TwigBasedEmail::class);

        $mailSender->send(
            $twigBasedEmail,
            [
                new Recipient('recipient@example.com', 'Test user'),
                new Recipient('cc@example.com', 'Test CC user', cc: true),
                new Recipient('bcc@example.com', 'Test BCC user', bcc: true),
            ],
            function (Configuration $configuration) {
                $configuration->setGreeting(false);
                $configuration->setSignature(false);
            },
        );

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $body = $email->getHtmlBody();
        $from = $email->getFrom()[0];
        $to = $email->getTo()[0];
        $cc = $email->getCc()[0];
        $bcc = $email->getBcc()[0];

        $this->assertEquals('test@example.com', $from->getAddress());
        $this->assertEquals('Mail sender name', $from->getName());
        $this->assertEquals('recipient@example.com', $to->getAddress());
        $this->assertEquals('Test user', $to->getName());
        $this->assertEquals('Test CC user', $cc->getName());
        $this->assertEquals('Test BCC user', $bcc->getName());
        $this->assertEquals('This is a test email', $email->getSubject());
        $this->assertStringContainsString('<title>This is a test email</title>', $body);
        $this->assertStringNotContainsString('<h1>Dear Test user,</h1>', $body);
        $this->assertStringContainsString('Content of the test email.', $body);
        $this->assertStringNotContainsString('Regards: Mail signatory', $body);
    }

    public function testTwigBasedEmailWithoutRecipient(): void
    {
        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $twigBasedEmail = $mailSender->getMailType(TwigBasedEmail::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('At least one recipient is required.');

        $mailSender->send($twigBasedEmail, [
            new Recipient('cc@example.com', 'Test CC user', cc: true),
            new Recipient('bcc@example.com', 'Test BCC user', bcc: true),
        ]);
    }

    protected function getEntityClass(): string|false
    {
        return false;
    }
}

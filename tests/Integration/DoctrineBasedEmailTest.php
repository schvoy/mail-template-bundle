<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Integration;

use Exception;
use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Schvoy\MailTemplateBundle\Mailer\MailSender;
use Schvoy\MailTemplateBundle\Mailer\Recipient;
use Schvoy\MailTemplateBundle\MailTemplateEntityInterface;
use Schvoy\MailTemplateBundle\Tests\AbstractTestCase;
use Schvoy\MailTemplateBundle\Tests\Fixtures\Email\DoctrineBasedEmail;
use Schvoy\MailTemplateBundle\Tests\Fixtures\Entity\Email;

class DoctrineBasedEmailTest extends AbstractTestCase
{
    public function testDoctrineBasedEmailUsedEntityContent(): void
    {
        $entity = new Email();
        $entity->setKey('test_email');
        $entity->setStatus(MailTemplateEntityInterface::STATUS_ACTIVE);
        $entity->setContent(
            <<<TWIG
            <h1>{{ 'email.greeting' | trans(configuration.parameters, configuration.translationDomain, configuration.locale) }}</h1>
        TWIG
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $doctrineBasedEmail = $mailSender->getMailType(DoctrineBasedEmail::class);

        $configuration = new Configuration();
        $configuration->setGreeting(true);
        $configuration->setSignature(true);
        $configuration->setMailType($doctrineBasedEmail);
        $configuration->setTranslationDomain('MailTemplateBundle');
        $configuration->setLocale('en');
        $configuration->addParameter('%userName%', 'Test user');

        $mailSender->send(
            $doctrineBasedEmail,
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
        $this->assertStringContainsString('<h1>Dear Test user,</h1>', $body);
    }

    public function testDoctrineBasedEmailUsedEntityTemplatePath(): void
    {
        $entity = new Email();
        $entity->setKey('test_email');
        $entity->setStatus(MailTemplateEntityInterface::STATUS_ACTIVE);
        $entity->setTemplatePath('@MailTemplate/mail/base_template.html.twig');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $doctrineBasedEmail = $mailSender->getMailType(DoctrineBasedEmail::class);

        $configuration = new Configuration();
        $configuration->setGreeting(true);
        $configuration->setSignature(true);
        $configuration->setMailType($doctrineBasedEmail);
        $configuration->setTranslationDomain('MailTemplateBundle');
        $configuration->setLocale('en');
        $configuration->addParameter('%userName%', 'Test user');

        $mailSender->send(
            $doctrineBasedEmail,
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

    public function testDoctrineBasedEmailButDatabaseWithoutContentAndTemplatePath(): void
    {
        $entity = new Email();
        $entity->setKey('test_email');
        $entity->setStatus(MailTemplateEntityInterface::STATUS_ACTIVE);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $mailType = $mailSender->getMailType(DoctrineBasedEmail::class);

        $configuration = new Configuration();
        $configuration->setGreeting(true);
        $configuration->setSignature(true);
        $configuration->setMailType($mailType);
        $configuration->setTranslationDomain('MailTemplateBundle');
        $configuration->setLocale('en');
        $configuration->addParameter('%userName%', 'Test user');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Content or templatePath is missing for %s', get_class($mailType)));

        $mailType->getContent($configuration);
    }

    public function testDoctrineBasedEmailButDatabaseEntryIsInactive(): void
    {
        $entity = new Email();
        $entity->setKey('test_email');
        $entity->setStatus(MailTemplateEntityInterface::STATUS_INACTIVE);
        $entity->setContent(
            <<<TWIG
            <h1>{{ 'email.greeting' | trans(configuration.parameters, configuration.translationDomain, configuration.locale) }}</h1>
        TWIG
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        /** @var MailSender $mailSender */
        $mailSender = self::getContainer()->get(MailSender::class);

        $mailType = $mailSender->getMailType(DoctrineBasedEmail::class);

        $configuration = new Configuration();
        $configuration->setGreeting(true);
        $configuration->setSignature(true);
        $configuration->setMailType($mailType);
        $configuration->setTranslationDomain('MailTemplateBundle');
        $configuration->setLocale('en');
        $configuration->addParameter('%userName%', 'Test user');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('There is no active database entry for %s', get_class($mailType)));

        $mailType->getContent($configuration);
    }

    protected function getEntityClass(): string|false
    {
        return false;
    }
}

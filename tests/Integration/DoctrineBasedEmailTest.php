<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Integration;

use Exception;
use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Schvoy\MailTemplateBundle\Mailer\MailSender;
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

        $mailType = $mailSender->getMailType(DoctrineBasedEmail::class);

        $configuration = new Configuration();
        $configuration->setGreeting(true);
        $configuration->setSignature(true);
        $configuration->setMailType($mailType);
        $configuration->setTranslationDomain('MailTemplateBundle');
        $configuration->setLocale('en');
        $configuration->addParameter('%userName%', 'Test user');

        $this->assertStringContainsString('<h1>Dear Test user,</h1>', $mailType->getContent($configuration));
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

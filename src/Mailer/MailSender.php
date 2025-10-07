<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

use Exception;
use Schvoy\MailTemplateBundle\DependencyInjection\MailTemplateExtension;
use Schvoy\MailTemplateBundle\Exceptions\MailTypeNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailSender
{
    private array $mailTypes = [];

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ParameterBagInterface $parameterBag,
        private readonly MailerInterface $mailer
    ) {
    }

    public function getMailType(string $mailTypeName): MailTypeInterface
    {
        if (!array_key_exists($mailTypeName, $this->mailTypes)) {
            throw new MailTypeNotFoundException();
        }

        return $this->mailTypes[$mailTypeName];
    }

    public function addMailType(MailTypeInterface $mailType): void
    {
        $this->mailTypes[get_class($mailType)] = $mailType;
    }

    /**
     * @param Recipient[] $recipients
     */
    public function send(
        MailTypeInterface $mailType,
        array $recipients = [],
        ?callable $extendConfiguration = null,
        ?callable $extendEmail = null,
    ): void {
        if (count($recipients) === 0) {
            return;
        }

        $to = [];
        $cc = [];
        $bcc = [];

        foreach ($recipients as $recipient) {
            if (!$recipient->isCc() && !$recipient->isBcc()) {
                $to[] = $recipient;
                continue;
            }

            if ($recipient->isCc()) {
                $cc[] = $recipient;
            }

            if ($recipient->isBcc()) {
                $bcc[] = $recipient;
            }
        }

        if (count($to) === 0) {
            throw new Exception('At least one recipient is required.');
        }

        foreach ($to as $recipient) {
            $email = $this->getEmail($mailType, $recipient, $extendConfiguration);

            foreach ($cc as $ccRecipient) {
                $email->addCc(new Address($ccRecipient->getEmail(), $ccRecipient->getName() ?? ''));
            }

            foreach ($bcc as $bccRecipient) {
                $email->addBcc(new Address($bccRecipient->getEmail(), $bccRecipient->getName() ?? ''));
            }

            if ($extendEmail) {
                $extendEmail($email);
            }

            $this->mailer->send($email);
        }
    }

    protected function getEmail(
        MailTypeInterface $mailType,
        Recipient $recipient,
        ?callable $extendConfiguration = null,
    ): Email {
        $configuration = $this->getEmailConfiguration($mailType, $recipient);

        if ($extendConfiguration) {
            $extendConfiguration($configuration);
        }

        $email = (new Email())
            ->from(
                new Address(
                    $this->parameterBag->get('mailer_sender_address'),
                    $this->parameterBag->get('mailer_sender_name') ?? ''
                )
            )
            ->to(new Address($recipient->getEmail(), $recipient->getName() ?? ''))
            ->subject(
                $this->translator->trans(
                    sprintf('%s.subject', $mailType->getKey()),
                    $configuration->getParameters(),
                    $configuration->getTranslationDomain(),
                    $configuration->getLocale()
                )
            )
            ->html($mailType->getContent($configuration));

        return $email;
    }

    protected function getEmailConfiguration(
        MailTypeInterface $mailType,
        Recipient $recipient
    ): Configuration {
        $configuration = new Configuration();
        $configuration->setMailType($mailType);
        $configuration->setTranslationDomain(
            $this->parameterBag->get(
                sprintf('%s.%s', MailTemplateExtension::ALIAS, 'translation_domain')
            )
        );
        $configuration->addParameter('_greetingNameExist_', (bool) $recipient->getName());
        $configuration->addParameter('%userName%', $recipient->getName());
        $configuration->addParameter('%signatory%', $this->parameterBag->get('mailer_signatory'));

        return $configuration;
    }
}

<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

use Schvoy\MailTemplateBundle\DependencyInjection\MailTemplateExtension;
use Schvoy\MailTemplateBundle\Exceptions\MailTypeNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailSender
{
    private const string DEFAULT_LOCALE = 'en';
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

    public function send(MailTypeInterface $mailType, array $recipients = [], array $configuration = []): void
    {
        if (count($recipients) === 0) {
            return;
        }

        /** @var Recipient $recipient */
        foreach ($recipients as $recipient) {
            $email = $this->getEmail($mailType, $recipient, $configuration);

            $this->mailer->send($email);
        }
    }

    protected function getEmail(MailTypeInterface $mailType, Recipient $recipient, array $configuration): Email
    {
        $configuration = $this->getEmailConfiguration($mailType, $recipient, $configuration);

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
                    $mailType->getSubject(),
                    $configuration['parameters'],
                    $configuration['__translationDomain'],
                    $configuration['__locale']
                )
            )
            ->html($mailType->getContent($configuration));

        return $email;
    }

    protected function getEmailConfiguration(
        MailTypeInterface $mailType,
        Recipient $recipient,
        array $configuration = []
    ): array {
        return array_replace_recursive(
            [
                '__greeting' => true,
                '__signature' => true,
                '__userName' => $recipient->getName() ?? false,
                '__mailType' => $mailType,
                '__translationDomain' => $this->parameterBag->get(
                    sprintf('%s.%s', MailTemplateExtension::ALIAS, 'translation_domain')
                ),
                '__locale' => self::DEFAULT_LOCALE,
                'parameters' => [
                    '%userName%' => $recipient->getName(),
                    '%signatory%' => $this->parameterBag->get('mailer_signatory'),
                ],
            ],
            $mailType->getConfiguration(),
            $configuration
        );
    }
}

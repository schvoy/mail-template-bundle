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

namespace EightMarq\MailTemplateBundle\Mailer;

use EightMarq\MailTemplateBundle\DependencyInjection\MailTemplateExtension;
use EightMarq\MailTemplateBundle\Exceptions\MailTypeNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailSender
{
    const DEFAULT_LOCALE = 'en';

    protected array $mailTypes = [];

    protected TranslatorInterface $translator;

    protected ParameterBagInterface $parameterBag;

    protected MailerInterface $mailer;

    public function __construct(
        TranslatorInterface $translator,
        ParameterBagInterface $parameterBag,
        MailerInterface $mailer
    )
    {
        $this->translator = $translator;
        $this->parameterBag = $parameterBag;
        $this->mailer = $mailer;
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

    public function send(MailTypeInterface $mailType, array $recipients = [], array $configuration = [])
    {
        if (count($recipients) > 0) {
            /** @var Recipient $recipient */
            foreach ($recipients as $recipient) {
                $configuration = $this->getEmailConfiguration($mailType, $configuration, $recipient);

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

                $this->mailer->send($email);
            }
        }
    }

    protected function getEmailConfiguration(
        MailTypeInterface $mailType,
        array $configuration,
        Recipient $recipient
    ): array
    {
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
            $configuration
        );
    }
}
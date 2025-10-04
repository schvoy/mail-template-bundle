<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

class Recipient
{
    public function __construct(
        private readonly string $email,
        private readonly ?string $name = null,
        private readonly bool $cc = false,
        private readonly bool $bcc = false,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isCc(): bool
    {
        return $this->cc;
    }

    public function isBcc(): bool
    {
        return $this->bcc;
    }
    }

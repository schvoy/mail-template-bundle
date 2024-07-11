<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

class Recipient
{
    public function __construct(private string $email, private ?string $name = null)
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

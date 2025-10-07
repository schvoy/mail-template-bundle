<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

interface MailTypeInterface
{
    public function getKey(): string;

    public function setKey(string $key): void;

    public function getContent(Configuration $configuration): string;
}

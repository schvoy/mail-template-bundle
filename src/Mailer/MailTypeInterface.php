<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

interface MailTypeInterface
{
    public function getSubject(): string;

    public function setSubject(string $subject): void;

    public function getBody(): string;

    public function setBody(string $body): void;

    public function getContent(array $configuration): string;

    public function getConfiguration(): array;

    public function setConfiguration(array $configuration): void;
}

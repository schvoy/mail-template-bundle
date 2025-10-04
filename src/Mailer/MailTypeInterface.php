<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

interface MailTypeInterface
{
    public function getTranslationKeyPath(): string;

    public function setTranslationKeyPath(string $translationKeyPath): void;

    public function getContent(Configuration $configuration): string;
}

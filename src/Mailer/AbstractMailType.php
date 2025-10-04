<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

abstract class AbstractMailType implements MailTypeInterface
{
    protected string $translationKeyPath;

    public function getTranslationKeyPath(): string
    {
        return $this->translationKeyPath;
    }

    public function setTranslationKeyPath(string $translationKeyPath): void
    {
        $this->translationKeyPath = $translationKeyPath;
    }
}

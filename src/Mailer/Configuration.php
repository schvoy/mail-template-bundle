<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

class Configuration
{
    private const string DEFAULT_LOCALE = 'en';

    private bool $greeting = true;
    private bool $signature = true;
    private MailTypeInterface $mailType;
    private string $translationDomain;
    private string $locale = self::DEFAULT_LOCALE;
    private array $parameters = [];

    public function isGreeting(): bool
    {
        return $this->greeting;
    }

    public function setGreeting(bool $greeting): void
    {
        $this->greeting = $greeting;
    }

    public function isSignature(): bool
    {
        return $this->signature;
    }

    public function setSignature(bool $signature): void
    {
        $this->signature = $signature;
    }

    public function getMailType(): MailTypeInterface
    {
        return $this->mailType;
    }

    public function setMailType(MailTypeInterface $mailType): void
    {
        $this->mailType = $mailType;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function addParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function removeParameter(string $key): void
    {
        if (isset($this->parameters[$key])) {
            unset($this->parameters[$key]);
        }
    }
}
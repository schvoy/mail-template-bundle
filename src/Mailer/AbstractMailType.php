<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer;

abstract class AbstractMailType implements MailTypeInterface
{
    protected string $key;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}

<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle;

interface MailTemplateEntityInterface
{
    public const string STATUS_ACTIVE = 'active';
    public const string STATUS_INACTIVE = 'inactive';

    public function getStatus(): string;

    public function getKey(): string;

    public function getTemplatePath(): string|null;

    public function getContent(): string|null;
}

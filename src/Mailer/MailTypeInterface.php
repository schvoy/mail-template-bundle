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

interface MailTypeInterface
{
    public function getSubject(): string;

    public function setSubject(string $subject): void;

    public function getBody(): string;

    public function setBody(string $body): void;

    public function getContent(array $configuration): string;
}
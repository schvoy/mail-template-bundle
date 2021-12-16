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

class Recipient
{
    protected ?string $name;

    protected string $email;

    public function __construct(string $email, ?string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
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
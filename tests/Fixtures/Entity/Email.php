<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests\Fixtures\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Schvoy\BaseEntityBundle\Entity\Interfaces\IdBasedEntityInterface;
use Schvoy\BaseEntityBundle\Entity\Traits\IdBasedEntityTrait;
use Schvoy\MailTemplateBundle\MailTemplateEntityInterface;

#[ORM\Entity]
#[ORM\Table]
class Email implements IdBasedEntityInterface, MailTemplateEntityInterface
{
    use IdBasedEntityTrait;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::STRING)]
    private string $key;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string|null $templatePath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string|null $content = null;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getTemplatePath(): string|null
    {
        return $this->templatePath;
    }

    public function setTemplatePath(string|null $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function getContent(): string|null
    {
        return $this->content;
    }

    public function setContent(string|null $content): void
    {
        $this->content = $content;
    }
}

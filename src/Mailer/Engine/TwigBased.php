<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer\Engine;

use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

trait TwigBased
{
    protected Environment $twig;

    #[Required]
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    public function getContent(Configuration $configuration): string
    {
        return $this->twig->render(
            $this->getTemplatePath() ?? '@MailTemplate/mail/base_template.html.twig',
            ['configuration' => $configuration]
        );
    }

    protected function getTemplatePath(): string|null
    {
        return null;
    }
}

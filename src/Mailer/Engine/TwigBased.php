<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer\Engine;

use Schvoy\MailTemplateBundle\DependencyInjection\MailTemplateExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Service\Attribute\Required;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Environment;

trait TwigBased
{
    protected Environment $twig;

    protected ParameterBagInterface $parameterBag;

    #[Required] public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    #[Required] public function setParameterBag(ParameterBagInterface $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    public function getContent(array $configuration = []): string
    {
        $baseTemplateParameter = sprintf('%s.%s', MailTemplateExtension::ALIAS, 'email_base_template');
        $baseCssParameter = sprintf('%s.%s', MailTemplateExtension::ALIAS, 'email_base_css_template');

        $html = $this->twig->render(
            $this->getTemplatePath() ?? $this->parameterBag->get($baseTemplateParameter),
            $configuration
        );

        $css = file_get_contents($this->parameterBag->get($baseCssParameter));

        return (new CssToInlineStyles())->convert($html, $css);
    }

    protected function getTemplatePath(): string|null
    {
        return null;
    }
}

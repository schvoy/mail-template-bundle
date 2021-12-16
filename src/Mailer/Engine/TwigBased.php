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

namespace EightMarq\MailTemplateBundle\Mailer\Engine;

use EightMarq\MailTemplateBundle\DependencyInjection\MailTemplateExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Environment;

trait TwigBased
{
    protected Environment $twig;

    protected ParameterBagInterface $parameterBag;

    /**
     * @required
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @required
     */
    public function setParameterBag(ParameterBagInterface $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    public function getContent(array $configuration = []): string
    {
        $html = $this->twig->render(
            $this->parameterBag->get(
                sprintf('%s.%s', MailTemplateExtension::ALIAS, 'email_base_template')
            ),
            $configuration
        );

        $cssToInlineStyles = new CssToInlineStyles();

        $css = file_get_contents(
            $this->parameterBag->get(
                sprintf('%s.%s', MailTemplateExtension::ALIAS, 'email_base_css_template')
            )
        );

        return $cssToInlineStyles->convert($html, $css);
    }
}
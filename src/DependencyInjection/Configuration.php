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

namespace EightMarq\MailTemplateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(MailTemplateExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('translation_domain')->defaultValue('MailTemplateBundle')->end()
            ->scalarNode('email_base_template')->defaultValue('@MailTemplateBundle/mail/base_template.html.twig')->end()
            ->scalarNode('email_base_css_template')->defaultValue(__DIR__ . '/../Resources/views/mail/email.css')->end()
            ->end();

        return $treeBuilder;
    }
}
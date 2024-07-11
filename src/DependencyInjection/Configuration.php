<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\DependencyInjection;

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
                ->scalarNode('translation_domain')
                    ->defaultValue('MailTemplateBundle')
                ->end()
                ->scalarNode('email_base_template')
                    ->defaultValue('@MailTemplateBundle/mail/base_template.html.twig')
                ->end()
                ->scalarNode('email_base_css_template')
                    ->defaultValue(__DIR__ . '/../Resources/views/mail/email.css')
                ->end()
            ->end();

        return $treeBuilder;
    }
}

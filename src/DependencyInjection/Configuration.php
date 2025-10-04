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
            ->end();

        return $treeBuilder;
    }
}

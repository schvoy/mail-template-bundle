<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\DependencyInjection;

use Schvoy\BaseEntityBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MailTemplateExtension extends AbstractExtension
{
    public const string ALIAS = 'mail_template_bundle';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter(
                sprintf('%s.%s', MailTemplateExtension::ALIAS, $key),
                $value
            );
        }
    }
}

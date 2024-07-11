<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\DependencyInjection\Compiler;

use Schvoy\MailTemplateBundle\Mailer\MailSender;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MailTypeRegisterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(MailSender::class)) {
            return;
        }

        $definition = $container->findDefinition(MailSender::class);

        $taggedServices = $container->findTaggedServiceIds('mail_template_bundle.mail.type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addMailType', [new Reference($id)]);
        }
    }
}

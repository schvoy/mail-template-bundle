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

namespace EightMarq\MailTemplateBundle\DependencyInjection\Compiler;

use EightMarq\MailTemplateBundle\Mailer\MailSender;
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

        $taggedServices = $container->findTaggedServiceIds('eightmarq.mail.type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addMailType', [new Reference($id)]);
        }
    }
}
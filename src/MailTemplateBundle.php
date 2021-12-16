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

namespace EightMarq\MailTemplateBundle;

use EightMarq\MailTemplateBundle\DependencyInjection\Compiler\MailTypeRegisterPass;
use EightMarq\MailTemplateBundle\Mailer\MailTypeInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MailTemplateBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(MailTypeInterface::class)
            ->addTag('eightmarq.mail.type');

        $container->addCompilerPass(new MailTypeRegisterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }
}
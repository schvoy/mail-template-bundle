<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle;

use Schvoy\MailTemplateBundle\DependencyInjection\Compiler\MailTypeRegisterPass;
use Schvoy\MailTemplateBundle\Mailer\MailTypeInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MailTemplateBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(MailTypeInterface::class)
            ->addTag('mail_template_bundle.mail.type');

        $container->addCompilerPass(new MailTypeRegisterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }
}

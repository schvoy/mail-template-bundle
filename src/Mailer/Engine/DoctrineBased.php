<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Mailer\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Schvoy\MailTemplateBundle\MailTemplateEntityInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

trait DoctrineBased
{
    protected Environment $twig;
    protected EntityManagerInterface $entityManager;

    #[Required]
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function getContent(Configuration $configuration): string
    {
        $entity = $this->entityManager->getRepository(MailTemplateEntityInterface::class)->findOneBy([
            'key' => $this->getKey(),
            'status' => MailTemplateEntityInterface::STATUS_ACTIVE,
        ]);

        if (!$entity) {
            throw new Exception(sprintf('There is no active database entry for %s', get_class($configuration->getMailType())));
        }

        $template = $entity->getTemplatePath() ?? '@MailTemplate/mail/base_template.html.twig';
        $parameters = ['configuration' => $configuration];

        if ($entity->getContent()) {
            $parameters['content'] = $entity->getContent();
            $template = '@MailTemplate/mail/string_loader_template.html.twig';
        }

        return $this->twig->render($template, $parameters);
    }

    protected function getEntityClass(): string|null
    {
        return null;
    }
}

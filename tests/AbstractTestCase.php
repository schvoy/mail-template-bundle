<?php

declare(strict_types=1);

namespace Schvoy\MailTemplateBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\CoversNothing;
use Schvoy\MailTemplateBundle\Tests\Services\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

#[CoversNothing]
abstract class AbstractTestCase extends KernelTestCase
{
    protected KernelInterface $testKernel;
    protected EntityManagerInterface $entityManager;
    protected ContainerInterface $container;

    private EntityRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel(['environment' => 'test', 'debug' => false]);

        $this->container = static::getContainer();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        if (false !== $this->getEntityClass()) {
            $this->repository = $this->entityManager->getRepository($this->getEntityClass());
        }

        /** @var DatabaseManager $databaseLoader */
        $databaseLoader = $this->container->get(DatabaseManager::class);
        $databaseLoader->reload();
    }

    protected function flush(): void
    {
        $this->entityManager->flush();
    }

    protected function getEntity(int|Uuid|Ulid|null $id): object
    {
        return $this->repository->find($id);
    }

    protected function createRequestContext(): void
    {
        $session = new Session(new MockFileSessionStorage());
        $session->start();

        $request = Request::createFromGlobals();
        $request->setSession($session);

        $requestStack = $this->container->get('request_stack');
        $requestStack->push($request);
    }

    abstract protected function getEntityClass(): string|bool;
}

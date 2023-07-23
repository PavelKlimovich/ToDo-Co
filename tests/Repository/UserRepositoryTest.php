<?php

namespace Tests\App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $managerRegistry;
    private $entityRepository;
    private $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityRepository = $this->createMock(EntityRepository::class);

        $this->repository = $this->createMock(UserRepository::class);
    }
    public function testSave()
    {
        $user = new User();
        $this->entityManager->expects($this->never())->method('flush');

        $this->repository->save($user);
    }
}

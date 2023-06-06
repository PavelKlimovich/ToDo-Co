<?php

namespace Tests\App\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
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
    
    $this->repository = new TaskRepository(
        $this->managerRegistry,
        $this->entityManager,
        $this->entityRepository
    );
}

    public function testSave()
    {
        $task = new Task();
        $this->entityManager->expects($this->once())->method('persist')->with($task);
        $this->entityManager->expects($this->never())->method('flush');

        $this->repository->save($task);
    }
}


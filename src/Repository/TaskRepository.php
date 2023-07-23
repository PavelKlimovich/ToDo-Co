<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Rerurn the list of tasks.
     *
     * @param string $type 
     * @return mixed
     */
    public function findTaskList(string $type): mixed
    {
        $entityManager = $this->getEntityManager();
        $isDone = '';

        switch ($type) {
            case 'ended':
                $isDone = true;
                break;
            case 'progress':
                $isDone = false;
                break;
            default:
                return $this->findAll();
        }

        $query = $entityManager->createQuery(
            'SELECT t
            FROM App\Entity\Task t
            WHERE t.isDone = :isDone
            ORDER BY t.createdAt ASC'
        )->setParameter('isDone', $isDone);

        return $query->getResult();
    }
}

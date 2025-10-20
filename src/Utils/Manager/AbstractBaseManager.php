<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template ModelEntity of object
 */
abstract class AbstractBaseManager
{

    public function __construct(
       protected EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * @return ObjectRepository
     */
   abstract public function getRepository(): ObjectRepository;

    /**
     * @param ModelEntity $entity
     * @return void
     */
    public function save(object $entity):void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param ModelEntity $entity
     * @return void
     */
    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function find(string $id): ?object
    {
        return $this->getRepository()->find($id);
    }

}

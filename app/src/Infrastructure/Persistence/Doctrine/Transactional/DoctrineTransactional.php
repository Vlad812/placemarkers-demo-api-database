<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Transactional;

use App\Application\Shared\TransactionalInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTransactional implements TransactionalInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(callable $operation): mixed
    {
        return $this->entityManager->wrapInTransaction($operation);
    }
}

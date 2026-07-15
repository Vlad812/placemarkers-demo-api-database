<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Placemarker;
use App\Domain\Repository\PlacemarkerRepositoryInterface;
use App\Domain\ValueObject\PlacemarkerId;
use App\Infrastructure\Persistence\Doctrine\EntityOrm\PlacemarkerOrm;
use App\Infrastructure\Persistence\Doctrine\Mapper\PlacemarkerMapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final readonly class DoctrinePlacemarkerRepository implements PlacemarkerRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Placemarker $placemarker): void
    {
        $id = $placemarker->id()->toString();
        $orm = $this->entityManager->find(PlacemarkerOrm::class, $id);

        if ($orm === null) {
            $orm = PlacemarkerMapper::toOrm($placemarker);
            $this->entityManager->persist($orm);
        } else {
            PlacemarkerMapper::updateOrmFromDomain($placemarker, $orm);
        }

        $this->entityManager->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function findById(PlacemarkerId $id): ?Placemarker
    {
        $orm = $this->entityManager->find(PlacemarkerOrm::class, $id->toString());

        if ($orm === null) {
            return null;
        }

        return PlacemarkerMapper::toDomain($orm);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(PlacemarkerId $id): void
    {
        $orm = $this->entityManager->find(PlacemarkerOrm::class, $id->toString());

        if ($orm !== null) {
            $this->entityManager->remove($orm);
            $this->entityManager->flush();
        }
    }
}

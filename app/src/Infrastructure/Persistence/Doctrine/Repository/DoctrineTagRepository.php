<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Tag;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\ValueObject\TagId;
use App\Infrastructure\Persistence\Doctrine\EntityOrm\TagOrm;
use App\Infrastructure\Persistence\Doctrine\Mapper\TagMapper;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final readonly class DoctrineTagRepository implements TagRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Tag $tag): void
    {
        $id = $tag->id()->toString();
        $orm = $this->entityManager->find(TagOrm::class, $id);

        if ($orm === null) {
            $orm = TagMapper::toOrm($tag);
            $this->entityManager->persist($orm);
        } else {
            TagMapper::updateOrmFromDomain($tag, $orm);
        }

        $this->entityManager->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function findById(TagId $id): ?Tag
    {
        $orm = $this->entityManager->find(TagOrm::class, $id->toString());

        if ($orm === null) {
            return null;
        }

        return TagMapper::toDomain($orm);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(TagId $id): void
    {
        $orm = $this->entityManager->find(TagOrm::class, $id->toString());

        if ($orm !== null) {
            $this->entityManager->remove($orm);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function findIdsOwnedByUserForType(string $userUuid, string $typeId, array $tagIds): array
    {
        if ($tagIds === []) {
            return [];
        }

        $result = $this->entityManager->getConnection()->executeQuery(
            'SELECT id FROM tags WHERE user_uuid = :user_uuid AND type_id = :type_id AND id IN (:ids)',
            [
                'user_uuid' => $userUuid,
                'type_id' => $typeId,
                'ids' => $tagIds,
            ],
            [
                'ids' => ArrayParameterType::STRING,
            ]
        )->fetchFirstColumn();

        return array_values(array_map(static fn (mixed $id): string => (string) $id, $result));
    }
}

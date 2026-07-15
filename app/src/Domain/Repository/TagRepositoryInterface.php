<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Tag;
use App\Domain\ValueObject\TagId;

interface TagRepositoryInterface
{
    public function save(Tag $tag): void;

    public function findById(TagId $id): ?Tag;

    public function delete(TagId $id): void;

    /**
     * Returns tag IDs that belong to the given user and type.
     *
     * @param list<string> $tagIds
     *
     * @return list<string>
     */
    public function findIdsOwnedByUserForType(string $userUuid, string $typeId, array $tagIds): array;
}

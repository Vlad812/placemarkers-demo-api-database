<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Exception\InvalidTagForTypeException;
use App\Domain\Repository\TagRepositoryInterface;

final readonly class PlacemarkerTagValidator
{
    public function __construct(
        private TagRepositoryInterface $tagRepository,
    ) {
    }

    /**
     * @param list<string> $tagIds
     *
     * @throws InvalidTagForTypeException
     */
    public function assertTagsBelongToType(string $userUuid, string $typeId, array $tagIds): void
    {
        if ($tagIds === []) {
            return;
        }

        $allowedTagIds = $this->tagRepository->findIdsOwnedByUserForType($userUuid, $typeId, $tagIds);
        $invalidTagIds = array_values(array_diff($tagIds, $allowedTagIds));

        if ($invalidTagIds !== []) {
            throw InvalidTagForTypeException::forType($typeId, $invalidTagIds);
        }
    }
}

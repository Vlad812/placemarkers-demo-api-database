<?php

declare(strict_types=1);

namespace App\Application\Command\CreateTag;

use App\Domain\Entity\Tag;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\Shared\UuidGeneratorInterface;
use App\Domain\ValueObject\TagId;

final readonly class CreateTagHandler
{
    public function __construct(
        private TagRepositoryInterface $repository,
        private UuidGeneratorInterface $uuidGenerator,
    ) {
    }

    public function __invoke(CreateTagCommand $command): Tag
    {
        $tag = Tag::create(
            TagId::fromString($this->uuidGenerator->generate()),
            $command->userUuid,
            $command->typeId,
            $command->name,
            $command->description,
        );

        $this->repository->save($tag);

        return $tag;
    }
}

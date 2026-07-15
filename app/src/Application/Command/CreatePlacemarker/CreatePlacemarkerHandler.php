<?php

declare(strict_types=1);

namespace App\Application\Command\CreatePlacemarker;

use App\Application\Service\PlacemarkerTagValidator;
use App\Domain\Entity\Placemarker;
use App\Domain\Repository\PlacemarkerRepositoryInterface;
use App\Domain\Shared\UuidGeneratorInterface;
use App\Domain\ValueObject\PlacemarkerId;

final readonly class CreatePlacemarkerHandler
{
    public function __construct(
        private PlacemarkerRepositoryInterface $repository,
        private PlacemarkerTagValidator $tagValidator,
        private UuidGeneratorInterface $uuidGenerator,
    ) {
    }

    public function __invoke(CreatePlacemarkerCommand $command): Placemarker
    {
        $this->tagValidator->assertTagsBelongToType(
            $command->userUuid->toString(),
            $command->typeId,
            $command->tagIds,
        );

        $placemarker = Placemarker::create(
            PlacemarkerId::fromString($this->uuidGenerator->generate()),
            $command->userUuid,
            $command->name,
            $command->coordinates,
            $command->description,
            $command->typeId,
            $command->tagIds,
        );

        $this->repository->save($placemarker);

        return $placemarker;
    }
}

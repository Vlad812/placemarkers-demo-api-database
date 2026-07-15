<?php

declare(strict_types=1);

namespace App\Application\Command\UpdatePlacemarker;

use App\Application\Service\PlacemarkerTagValidator;
use App\Domain\Entity\Placemarker;
use App\Domain\Exception\PlacemarkerNotFoundException;
use App\Domain\Repository\PlacemarkerRepositoryInterface;

final readonly class UpdatePlacemarkerHandler
{
    public function __construct(
        private PlacemarkerRepositoryInterface $repository,
        private PlacemarkerTagValidator $tagValidator,
    ) {
    }

    public function __invoke(UpdatePlacemarkerCommand $command): Placemarker
    {
        $placemarker = $this->repository->findById($command->id);

        if ($placemarker === null) {
            throw PlacemarkerNotFoundException::withId($command->id->toString());
        }

        $placemarker->changeName($command->name);
        $placemarker->changeDescription($command->description);

        if ($command->typeId !== null) {
            $placemarker->changeTypeId($command->typeId);
        }

        if ($command->tagIds !== null) {
            $placemarker->changeTagIds($command->tagIds);
        }

        $this->tagValidator->assertTagsBelongToType(
            $placemarker->userUuid()->toString(),
            $placemarker->typeId(),
            $placemarker->tagIds(),
        );

        $this->repository->save($placemarker);

        return $placemarker;
    }
}

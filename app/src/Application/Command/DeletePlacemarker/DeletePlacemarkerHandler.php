<?php

declare(strict_types=1);

namespace App\Application\Command\DeletePlacemarker;

use App\Domain\Exception\PlacemarkerNotFoundException;
use App\Domain\Repository\PlacemarkerRepositoryInterface;

final readonly class DeletePlacemarkerHandler
{
    public function __construct(
        private PlacemarkerRepositoryInterface $repository,
    ) {
    }

    public function __invoke(DeletePlacemarkerCommand $command): void
    {
        if ($this->repository->findById($command->id) === null) {
            throw PlacemarkerNotFoundException::withId($command->id->toString());
        }

        $this->repository->delete($command->id);
    }
}

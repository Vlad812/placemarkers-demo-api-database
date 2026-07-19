<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\DeletePlacemarker;

use App\Application\Command\DeletePlacemarker\DeletePlacemarkerCommand;
use App\Application\Command\DeletePlacemarker\DeletePlacemarkerHandler;
use App\Domain\Entity\Placemarker;
use App\Domain\Exception\PlacemarkerNotFoundException;
use App\Domain\Repository\PlacemarkerRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerId;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;
use PHPUnit\Framework\TestCase;

final class DeletePlacemarkerHandlerTest extends TestCase
{
    private const PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174001';

    public function testInvokeDeletesExistingPlacemarker(): void
    {
        $command = new DeletePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
        );

        $placemarker = Placemarker::create(
            $command->id,
            UserUuid::fromString('123e4567-e89b-12d3-a456-426614174000'),
            PlacemarkerName::fromString('Test Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Description'),
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($command->id)
            ->willReturn($placemarker);
        $repository->expects($this->once())
            ->method('delete')
            ->with($command->id);

        $handler = new DeletePlacemarkerHandler($repository);

        $handler($command);
    }

    public function testInvokeThrowsWhenPlacemarkerNotFound(): void
    {
        $command = new DeletePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($command->id)
            ->willReturn(null);
        $repository->expects($this->never())->method('delete');

        $handler = new DeletePlacemarkerHandler($repository);

        $this->expectException(PlacemarkerNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Placemarker with id "%s" was not found.', self::PLACEMARKER_ID));

        $handler($command);
    }
}

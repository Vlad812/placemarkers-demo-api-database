<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\UpdatePlacemarker;

use App\Application\Command\UpdatePlacemarker\UpdatePlacemarkerCommand;
use App\Application\Command\UpdatePlacemarker\UpdatePlacemarkerHandler;
use App\Application\Service\PlacemarkerTagValidator;
use App\Domain\Entity\Placemarker;
use App\Domain\Exception\InvalidTagForTypeException;
use App\Domain\Exception\PlacemarkerNotFoundException;
use App\Domain\Repository\PlacemarkerRepositoryInterface;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerId;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;
use PHPUnit\Framework\TestCase;

final class UpdatePlacemarkerHandlerTest extends TestCase
{
    private const string PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174001';

    public function testInvokeUpdatesPlacemarker(): void
    {
        $command = new UpdatePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
            PlacemarkerName::fromString('Updated Point'),
            Description::fromString('Updated description'),
            'cafe',
        );

        $placemarker = Placemarker::create(
            $command->id,
            UserUuid::fromString('123e4567-e89b-12d3-a456-426614174000'),
            PlacemarkerName::fromString('Old Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Old description'),
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($command->id)
            ->willReturn($placemarker);
        $repository->expects($this->once())->method('save')->with($placemarker);

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->never())->method('findIdsOwnedByUserForType');

        $handler = new UpdatePlacemarkerHandler($repository, new PlacemarkerTagValidator($tagRepository));
        $result = $handler($command);

        $this->assertSame('Updated Point', $result->name()->toString());
        $this->assertSame('Updated description', $result->description()->toString());
        $this->assertSame('cafe', $result->typeId());
    }

    public function testInvokeValidatesAndStoresTagsWhenProvided(): void
    {
        $command = new UpdatePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
            PlacemarkerName::fromString('Updated Point'),
            Description::fromString('Updated description'),
            'cafe',
            ['tag-1', 'tag-2'],
        );

        $placemarker = Placemarker::create(
            $command->id,
            UserUuid::fromString('123e4567-e89b-12d3-a456-426614174000'),
            PlacemarkerName::fromString('Old Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Old description'),
            'cafe',
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->willReturn($placemarker);
        $repository->expects($this->once())->method('save');

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->once())
            ->method('findIdsOwnedByUserForType')
            ->with('123e4567-e89b-12d3-a456-426614174000', 'cafe', ['tag-1', 'tag-2'])
            ->willReturn(['tag-1', 'tag-2']);

        $handler = new UpdatePlacemarkerHandler($repository, new PlacemarkerTagValidator($tagRepository));

        $result = $handler($command);

        $this->assertSame(['tag-1', 'tag-2'], $result->tagIds());
    }

    public function testInvokeThrowsWhenPlacemarkerNotFound(): void
    {
        $command = new UpdatePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
            PlacemarkerName::fromString('Updated Point'),
            Description::fromString('Updated description'),
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('save');

        $tagRepository = $this->createStub(TagRepositoryInterface::class);

        $handler = new UpdatePlacemarkerHandler($repository, new PlacemarkerTagValidator($tagRepository));

        $this->expectException(PlacemarkerNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Placemarker with id "%s" was not found.', self::PLACEMARKER_ID));

        $handler($command);
    }

    public function testInvokeThrowsWhenTagsDoNotBelongToType(): void
    {
        $command = new UpdatePlacemarkerCommand(
            PlacemarkerId::fromString(self::PLACEMARKER_ID),
            PlacemarkerName::fromString('Updated Point'),
            Description::fromString('Updated description'),
            'cafe',
            ['tag-1', 'tag-2'],
        );

        $placemarker = Placemarker::create(
            $command->id,
            UserUuid::fromString('123e4567-e89b-12d3-a456-426614174000'),
            PlacemarkerName::fromString('Old Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Old description'),
            'cafe',
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->willReturn($placemarker);
        $repository->expects($this->never())->method('save');

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->once())
            ->method('findIdsOwnedByUserForType')
            ->willReturn(['tag-1']);

        $handler = new UpdatePlacemarkerHandler($repository, new PlacemarkerTagValidator($tagRepository));

        $this->expectException(InvalidTagForTypeException::class);
        $this->expectExceptionMessage('Tags [tag-2] are not allowed for type "cafe".');

        $handler($command);
    }
}

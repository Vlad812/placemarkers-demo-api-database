<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreatePlacemarker;

use App\Application\Command\CreatePlacemarker\CreatePlacemarkerCommand;
use App\Application\Command\CreatePlacemarker\CreatePlacemarkerHandler;
use App\Application\Service\PlacemarkerTagValidator;
use App\Domain\Entity\Placemarker;
use App\Domain\Exception\InvalidTagForTypeException;
use App\Domain\Repository\PlacemarkerRepositoryInterface;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\Shared\UuidGeneratorInterface;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;
use PHPUnit\Framework\TestCase;

final class CreatePlacemarkerHandlerTest extends TestCase
{
    private const string PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174001';
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testInvokeCreatesAndSavesPlacemarker(): void
    {
        $command = new CreatePlacemarkerCommand(
            UserUuid::fromString(self::USER_UUID),
            PlacemarkerName::fromString('Test Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Description'),
            'default',
            [],
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(static function (Placemarker $placemarker): bool {
                return $placemarker->id()->toString() === self::PLACEMARKER_ID;
            }));

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->never())->method('findIdsOwnedByUserForType');

        $uuidGenerator = $this->createMock(UuidGeneratorInterface::class);
        $uuidGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(self::PLACEMARKER_ID);

        $handler = new CreatePlacemarkerHandler(
            $repository,
            new PlacemarkerTagValidator($tagRepository),
            $uuidGenerator,
        );
        $placemarker = $handler($command);

        $this->assertSame(self::PLACEMARKER_ID, $placemarker->id()->toString());
        $this->assertSame('Test Point', $placemarker->name()->toString());
        $this->assertSame(45.0, $placemarker->coordinates()->latitude());
    }

    public function testInvokeValidatesAndStoresTagsWhenProvided(): void
    {
        $command = new CreatePlacemarkerCommand(
            UserUuid::fromString(self::USER_UUID),
            PlacemarkerName::fromString('Test Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Description'),
            'cafe',
            ['tag-1', 'tag-2'],
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(static function (Placemarker $placemarker): bool {
                return $placemarker->tagIds() === ['tag-1', 'tag-2']
                    && $placemarker->typeId() === 'cafe'
                    && $placemarker->id()->toString() === self::PLACEMARKER_ID;
            }));

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->once())
            ->method('findIdsOwnedByUserForType')
            ->with(self::USER_UUID, 'cafe', ['tag-1', 'tag-2'])
            ->willReturn(['tag-1', 'tag-2']);

        $uuidGenerator = $this->createMock(UuidGeneratorInterface::class);
        $uuidGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(self::PLACEMARKER_ID);

        $handler = new CreatePlacemarkerHandler(
            $repository,
            new PlacemarkerTagValidator($tagRepository),
            $uuidGenerator,
        );

        $handler($command);
    }

    public function testInvokeThrowsWhenTagsDoNotBelongToType(): void
    {
        $command = new CreatePlacemarkerCommand(
            UserUuid::fromString(self::USER_UUID),
            PlacemarkerName::fromString('Test Point'),
            Coordinates::fromFloats(45.0, 90.0),
            Description::fromString('Description'),
            'cafe',
            ['tag-1', 'tag-2'],
        );

        $repository = $this->createMock(PlacemarkerRepositoryInterface::class);
        $repository->expects($this->never())->method('save');

        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository->expects($this->once())
            ->method('findIdsOwnedByUserForType')
            ->willReturn(['tag-1']);

        $uuidGenerator = $this->createMock(UuidGeneratorInterface::class);
        $uuidGenerator->expects($this->never())->method('generate');

        $handler = new CreatePlacemarkerHandler(
            $repository,
            new PlacemarkerTagValidator($tagRepository),
            $uuidGenerator,
        );

        $this->expectException(InvalidTagForTypeException::class);
        $this->expectExceptionMessage('Tags [tag-2] are not allowed for type "cafe".');

        $handler($command);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreateTag;

use App\Application\Command\CreateTag\CreateTagCommand;
use App\Application\Command\CreateTag\CreateTagHandler;
use App\Domain\Entity\Tag;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\Shared\UuidGeneratorInterface;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\TagName;
use App\Domain\ValueObject\UserUuid;
use PHPUnit\Framework\TestCase;

final class CreateTagHandlerTest extends TestCase
{
    private const string TAG_ID = '323e4567-e89b-12d3-a456-426614174002';
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testInvokeCreatesAndSavesTag(): void
    {
        $command = new CreateTagCommand(
            UserUuid::fromString(self::USER_UUID),
            'cafe',
            TagName::fromString('Food'),
            Description::fromString('Food places'),
        );

        $repository = $this->createMock(TagRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(static function (Tag $tag): bool {
                return $tag->id()->toString() === self::TAG_ID;
            }));

        $uuidGenerator = $this->createMock(UuidGeneratorInterface::class);
        $uuidGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(self::TAG_ID);

        $handler = new CreateTagHandler($repository, $uuidGenerator);
        $tag = $handler($command);

        $this->assertSame(self::TAG_ID, $tag->id()->toString());
        $this->assertSame('Food', $tag->name()->toString());
        $this->assertSame('cafe', $tag->typeId());
        $this->assertSame('Food places', $tag->description()->toString());
    }
}

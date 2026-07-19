<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\UpdatePlacemarker;

use App\Application\Command\UpdatePlacemarker\UpdatePlacemarkerCommand;
use App\Application\Exception\InvalidParameter;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class UpdatePlacemarkerCommandTest extends TestCase
{
    private const PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174001';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = UpdatePlacemarkerCommand::createFromRawValues(self::PLACEMARKER_ID, [
            'name' => 'Updated Point',
            'description' => 'Updated description',
            'type_id' => 'cafe',
            'tags' => ['tag-1', 'tag-2'],
        ]);

        $this->assertSame(self::PLACEMARKER_ID, $command->id->toString());
        $this->assertSame('Updated Point', $command->name->toString());
        $this->assertSame('Updated description', $command->description->toString());
        $this->assertSame('cafe', $command->typeId);
        $this->assertSame(['tag-1', 'tag-2'], $command->tagIds);
    }

    public function testCreateFromRawValuesWithoutOptionalFields(): void
    {
        $command = UpdatePlacemarkerCommand::createFromRawValues(self::PLACEMARKER_ID, [
            'name' => 'Updated Point',
        ]);

        $this->assertSame('', $command->description->toString());
        $this->assertNull($command->typeId);
        $this->assertNull($command->tagIds);
    }

    public function testCreateFromRawValuesMissingNameThrowsException(): void
    {
        $this->expectException(InvalidParameter::class);
        $this->expectExceptionMessage('Missing required parameter "name".');

        UpdatePlacemarkerCommand::createFromRawValues(self::PLACEMARKER_ID, []);
    }

    public function testCreateFromRawValuesInvalidUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UpdatePlacemarkerCommand::createFromRawValues('not-a-uuid', ['name' => 'Test']);
    }
}

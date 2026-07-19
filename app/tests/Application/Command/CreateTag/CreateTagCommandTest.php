<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreateTag;

use App\Application\Command\CreateTag\CreateTagCommand;
use App\Application\Exception\InvalidParameter;
use PHPUnit\Framework\TestCase;

final class CreateTagCommandTest extends TestCase
{
    private const string USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = CreateTagCommand::createFromRawValues([
            'name' => 'Food',
            'type_id' => 'cafe',
            'description' => 'Food places',
        ], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $command->userUuid->toString());
        $this->assertSame('cafe', $command->typeId);
        $this->assertSame('Food', $command->name->toString());
        $this->assertSame('Food places', $command->description->toString());
    }

    public function testCreateFromRawValuesUsesEmptyDescriptionWhenMissing(): void
    {
        $command = CreateTagCommand::createFromRawValues([
            'name' => 'Food',
            'type_id' => 'cafe',
        ], self::USER_UUID);

        $this->assertSame('', $command->description->toString());
    }

    public function testCreateFromRawValuesMissingNameThrowsException(): void
    {
        $this->expectException(InvalidParameter::class);
        $this->expectExceptionMessage('Missing required parameter "name".');

        CreateTagCommand::createFromRawValues([
            'type_id' => 'cafe',
        ], self::USER_UUID);
    }

    public function testCreateFromRawValuesMissingTypeIdThrowsException(): void
    {
        $this->expectException(InvalidParameter::class);
        $this->expectExceptionMessage('Missing required parameter "type_id".');

        CreateTagCommand::createFromRawValues([
            'name' => 'Food',
        ], self::USER_UUID);
    }
}

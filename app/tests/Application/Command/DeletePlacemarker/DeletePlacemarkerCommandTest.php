<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\DeletePlacemarker;

use App\Application\Command\DeletePlacemarker\DeletePlacemarkerCommand;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class DeletePlacemarkerCommandTest extends TestCase
{
    private const PLACEMARKER_ID = '223e4567-e89b-12d3-a456-426614174001';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = DeletePlacemarkerCommand::createFromRawValues(self::PLACEMARKER_ID);

        $this->assertSame(self::PLACEMARKER_ID, $command->id->toString());
    }

    public function testCreateFromRawValuesInvalidUuidThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DeletePlacemarkerCommand::createFromRawValues('not-a-uuid');
    }
}

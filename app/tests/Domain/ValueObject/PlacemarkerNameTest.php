<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\PlacemarkerName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PlacemarkerNameTest extends TestCase
{
    public function testFromStringTrimsAndReturnsValue(): void
    {
        $name = PlacemarkerName::fromString('  Test Point  ');

        $this->assertSame('Test Point', $name->toString());
    }

    public function testEmptyNameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Placemarker name must not be empty.');

        PlacemarkerName::fromString('   ');
    }
}

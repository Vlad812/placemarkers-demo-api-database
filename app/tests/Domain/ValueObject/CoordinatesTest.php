<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\Exception\InvalidCoordinatesException;
use App\Domain\ValueObject\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CoordinatesTest extends TestCase
{
    public function testFromFloatsSuccess(): void
    {
        $coordinates = Coordinates::fromFloats(45.5, 90.5);

        $this->assertSame(45.5, $coordinates->latitude());
        $this->assertSame(90.5, $coordinates->longitude());
    }

    #[DataProvider('invalidLatitudeProvider')]
    public function testInvalidLatitudeThrowsException(float $latitude): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Invalid latitude');

        Coordinates::fromFloats($latitude, 0.0);
    }

    public static function invalidLatitudeProvider(): array
    {
        return [
            [-90.1],
            [90.1],
        ];
    }

    #[DataProvider('invalidLongitudeProvider')]
    public function testInvalidLongitudeThrowsException(float $longitude): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Invalid longitude');

        Coordinates::fromFloats(0.0, $longitude);
    }

    public static function invalidLongitudeProvider(): array
    {
        return [
            [-180.1],
            [180.1],
        ];
    }
}

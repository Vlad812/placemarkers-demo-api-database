<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\CreatePlacemarker;

use App\Application\Command\CreatePlacemarker\CreatePlacemarkerCommand;
use App\Application\Exception\InvalidParameter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class CreatePlacemarkerCommandTest extends TestCase
{
    private const USER_UUID = '123e4567-e89b-12d3-a456-426614174000';

    public function testCreateFromRawValuesSuccess(): void
    {
        $command = CreatePlacemarkerCommand::createFromRawValues([
            'name' => 'Test Point',
            'lat' => '45.0',
            'lon' => '90.0',
            'description' => 'A description',
            'type_id' => 'cafe',
            'tags' => ['tag-1', 'tag-2'],
        ], self::USER_UUID);

        $this->assertSame(self::USER_UUID, $command->userUuid->toString());
        $this->assertSame('Test Point', $command->name->toString());
        $this->assertSame(45.0, $command->coordinates->latitude());
        $this->assertSame(90.0, $command->coordinates->longitude());
        $this->assertSame('A description', $command->description->toString());
        $this->assertSame('cafe', $command->typeId);
        $this->assertSame(['tag-1', 'tag-2'], $command->tagIds);
    }

    #[DataProvider('missingFieldProvider')]
    public function testCreateFromRawValuesMissingFieldThrowsException(array $requestData, string $field): void
    {
        $this->expectException(InvalidParameter::class);
        $this->expectExceptionMessage(sprintf('Missing required parameter "%s".', $field));

        CreatePlacemarkerCommand::createFromRawValues($requestData, self::USER_UUID);
    }

    public static function missingFieldProvider(): array
    {
        $base = [
            'name' => 'Test',
            'lat' => '45.0',
            'lon' => '90.0',
        ];

        return [
            'missing name' => [array_diff_key($base, ['name' => '']), 'name'],
            'missing lat' => [array_diff_key($base, ['lat' => '']), 'lat'],
            'missing lon' => [array_diff_key($base, ['lon' => '']), 'lon'],
        ];
    }

    public function testCreateFromRawValuesInvalidCoordinatesThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a numeric. Got: string');

        CreatePlacemarkerCommand::createFromRawValues([
            'name' => 'Test',
            'lat' => 'abc',
            'lon' => '90.0',
        ], self::USER_UUID);
    }
}

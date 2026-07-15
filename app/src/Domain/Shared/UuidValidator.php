<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use InvalidArgumentException;

final class UuidValidator
{
    private const string PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public static function assertValid(string $uuid): void
    {
        if (preg_match(self::PATTERN, $uuid) !== 1) {
            throw new InvalidArgumentException(sprintf('Value "%s" is not a valid UUID.', $uuid));
        }
    }
}

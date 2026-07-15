<?php

declare(strict_types=1);

namespace App\Application\Exception;

use InvalidArgumentException;

final class InvalidParameter extends InvalidArgumentException
{
    public static function invalidType(string $field, string $expectedType): self
    {
        return new self(sprintf('Invalid parameter "%s": expected %s.', $field, $expectedType));
    }

    public static function missing(string $field): self
    {
        return new self(sprintf('Missing required parameter "%s".', $field));
    }
}

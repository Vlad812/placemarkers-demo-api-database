<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class PlacemarkerType
{
    private const int MAX_LENGTH = 50;

    private function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $value): self
    {
        if ($value === '') {
            throw new InvalidArgumentException('Type cannot be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Type is too long.');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}

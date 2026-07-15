<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class TagName
{
    private const int MAX_LENGTH = 255;

    private function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Tag name is too long.');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}

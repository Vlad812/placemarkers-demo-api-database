<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Description
{
    private const int MAX_LENGTH = 2000;

    private function __construct(
        private string $value,
    ) {
    }

    public static function empty(): self
    {
        return new self('');
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'Description must not exceed %d characters.',
                self::MAX_LENGTH,
            ));
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }
}

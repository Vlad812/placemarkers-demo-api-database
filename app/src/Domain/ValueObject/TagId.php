<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Shared\UuidValidator;

final readonly class TagId
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        UuidValidator::assertValid($value);

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

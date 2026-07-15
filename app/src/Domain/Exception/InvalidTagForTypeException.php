<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

final class InvalidTagForTypeException extends DomainException
{
    /**
     * @param list<string> $invalidTagIds
     */
    public static function forType(string $typeId, array $invalidTagIds): self
    {
        return new self(sprintf(
            'Tags [%s] are not allowed for type "%s".',
            implode(', ', $invalidTagIds),
            $typeId,
        ));
    }
}

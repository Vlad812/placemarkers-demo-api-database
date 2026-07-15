<?php

declare(strict_types=1);

namespace App\Application\Command\DeletePlacemarker;

use App\Domain\ValueObject\PlacemarkerId;
use Webmozart\Assert\Assert;

final readonly class DeletePlacemarkerCommand
{
    public function __construct(
        public PlacemarkerId $id,
    ) {
    }

    public static function createFromRawValues(string $id): self
    {
        Assert::uuid($id);

        return new self(PlacemarkerId::fromString($id));
    }
}

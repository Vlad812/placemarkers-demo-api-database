<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Placemarker;
use App\Domain\ValueObject\PlacemarkerId;

interface PlacemarkerRepositoryInterface
{
    public function save(Placemarker $placemarker): void;

    public function findById(PlacemarkerId $id): ?Placemarker;

    public function delete(PlacemarkerId $id): void;
}

<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerId;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;

final class Placemarker
{
    public const string DEFAULT_TYPE = 'default';

    private function __construct(
        private readonly PlacemarkerId $id,
        private readonly UserUuid      $userUuid,
        private PlacemarkerName        $name,
        private Coordinates            $coordinates,
        private Description            $description,
        private string                 $typeId,
        /** @var list<string> */
        private array                  $tagIds = [],
    ) {
    }

    /**
     * @param list<string> $tagIds
     */
    public static function create(
        PlacemarkerId $id,
        UserUuid $userUuid,
        PlacemarkerName $name,
        Coordinates $coordinates,
        Description $description,
        string $typeId = self::DEFAULT_TYPE,
        array $tagIds = [],
    ): self {
        return new self($id, $userUuid, $name, $coordinates, $description, $typeId, $tagIds);
    }

    public function id(): PlacemarkerId
    {
        return $this->id;
    }

    public function userUuid(): UserUuid
    {
        return $this->userUuid;
    }

    public function name(): PlacemarkerName
    {
        return $this->name;
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function description(): Description
    {
        return $this->description;
    }

    public function changeName(PlacemarkerName $name): void
    {
        $this->name = $name;
    }

    public function changeCoordinates(Coordinates $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    public function changeDescription(Description $description): void
    {
        $this->description = $description;
    }

    public function typeId(): string
    {
        return $this->typeId;
    }

    public function changeTypeId(string $typeId): void
    {
        $this->typeId = $typeId;
    }

    /**
     * @return list<string>
     */
    public function tagIds(): array
    {
        return $this->tagIds;
    }

    /**
     * @param list<string> $tagIds
     */
    public function changeTagIds(array $tagIds): void
    {
        $this->tagIds = $tagIds;
    }
}

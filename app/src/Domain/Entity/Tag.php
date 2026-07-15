<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\TagId;
use App\Domain\ValueObject\TagName;
use App\Domain\ValueObject\UserUuid;

final class Tag
{
    private function __construct(
        private readonly TagId    $id,
        private readonly UserUuid $userUuid,
        private readonly string   $typeId,
        private TagName           $name,
        private Description       $description,
    ) {
    }

    public static function create(
        TagId $id,
        UserUuid $userUuid,
        string $typeId,
        TagName $name,
        Description $description,
    ): self {
        return new self($id, $userUuid, $typeId, $name, $description);
    }

    public function id(): TagId
    {
        return $this->id;
    }

    public function userUuid(): UserUuid
    {
        return $this->userUuid;
    }

    public function typeId(): string
    {
        return $this->typeId;
    }

    public function name(): TagName
    {
        return $this->name;
    }

    public function description(): Description
    {
        return $this->description;
    }

    public function changeName(TagName $name): void
    {
        $this->name = $name;
    }

    public function changeDescription(Description $description): void
    {
        $this->description = $description;
    }
}

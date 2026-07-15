<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Domain\Entity\Tag;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\TagId;
use App\Domain\ValueObject\TagName;
use App\Domain\ValueObject\UserUuid;
use App\Infrastructure\Persistence\Doctrine\EntityOrm\TagOrm;
use DateTimeImmutable;

final class TagMapper
{
    public static function toDomain(TagOrm $orm): Tag
    {
        return Tag::create(
            TagId::fromString($orm->getId()),
            UserUuid::fromString($orm->getUserUuid()),
            $orm->getTypeId(),
            TagName::fromString($orm->getName()),
            Description::fromString($orm->getDescription() ?? ''),
        );
    }

    public static function toOrm(Tag $domain): TagOrm
    {
        return new TagOrm(
            $domain->id()->toString(),
            $domain->userUuid()->toString(),
            $domain->typeId(),
            $domain->name()->toString(),
            $domain->description()->toString(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }

    public static function updateOrmFromDomain(Tag $domain, TagOrm $orm): void
    {
        $orm->setName($domain->name()->toString());
        $orm->setDescription($domain->description()->toString());
        $orm->setUpdatedAt(new DateTimeImmutable());
    }
}

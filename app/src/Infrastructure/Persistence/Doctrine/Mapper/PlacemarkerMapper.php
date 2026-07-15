<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapper;

use App\Domain\Entity\Placemarker;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerId;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;
use App\Infrastructure\Persistence\Doctrine\EntityOrm\PlacemarkerOrm;
use DateTimeImmutable;

final class PlacemarkerMapper
{
    public static function toDomain(PlacemarkerOrm $orm): Placemarker
    {
        return Placemarker::create(
            PlacemarkerId::fromString($orm->getId()),
            UserUuid::fromString($orm->getUserUuid()),
            PlacemarkerName::fromString($orm->getName()),
            Coordinates::fromFloats($orm->getLat(), $orm->getLon()),
            Description::fromString($orm->getDescription() ?? ''),
            $orm->getTypeId(),
            $orm->getTagsJsonb(),
        );
    }

    public static function toOrm(Placemarker $domain): PlacemarkerOrm
    {
        return new PlacemarkerOrm(
            $domain->id()->toString(),
            $domain->userUuid()->toString(),
            $domain->name()->toString(),
            $domain->coordinates()->latitude(),
            $domain->coordinates()->longitude(),
            $domain->description()->toString(),
            $domain->typeId(),
            $domain->tagIds(),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
    }

    public static function updateOrmFromDomain(Placemarker $domain, PlacemarkerOrm $orm): void
    {
        $orm->setName($domain->name()->toString());
        $orm->setLat($domain->coordinates()->latitude());
        $orm->setLon($domain->coordinates()->longitude());
        $orm->setDescription($domain->description()->toString());
        $orm->setTypeId($domain->typeId());
        $orm->setTagsJsonb($domain->tagIds());
        $orm->setUpdatedAt(new DateTimeImmutable());
    }
}

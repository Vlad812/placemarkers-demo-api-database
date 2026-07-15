<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\EntityOrm;

use App\Domain\Entity\Placemarker;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'placemarkers')]
#[ORM\HasLifecycleCallbacks]
class PlacemarkerOrm
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    private string $id;

    #[ORM\Column(name: 'user_uuid', type: Types::GUID)]
    private string $userUuid;

    #[ORM\Column(name: 'type_id', type: Types::STRING, length: 50, nullable: false, options: ['default' => Placemarker::DEFAULT_TYPE])]
    private string $typeId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::FLOAT)]
    private float $lat;

    #[ORM\Column(type: Types::FLOAT)]
    private float $lon;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /** @var list<string> */
    #[ORM\Column(name: 'tags_jsonb', type: Types::JSON, options: ['jsonb' => true])]
    private array $tagsJsonb = [];

    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    /**
     * @param list<string> $tagsJsonb
     */
    public function __construct(
        string $id,
        string $userUuid,
        string $name,
        float $lat,
        float $lon,
        ?string $description,
        string $typeId,
        array $tagsJsonb,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->userUuid = $userUuid;
        $this->name = $name;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->description = $description;
        $this->typeId = $typeId;
        $this->tagsJsonb = array_values($tagsJsonb);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): void
    {
        $this->lat = $lat;
    }

    public function getLon(): float
    {
        return $this->lon;
    }

    public function setLon(float $lon): void
    {
        $this->lon = $lon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTypeId(): string
    {
        return $this->typeId;
    }

    public function setTypeId(string $typeId): void
    {
        $this->typeId = $typeId;
    }

    /**
     * @return list<string>
     */
    public function getTagsJsonb(): array
    {
        return $this->tagsJsonb;
    }

    /**
     * @param list<string> $tagsJsonb
     */
    public function setTagsJsonb(array $tagsJsonb): void
    {
        $this->tagsJsonb = array_values($tagsJsonb);
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}

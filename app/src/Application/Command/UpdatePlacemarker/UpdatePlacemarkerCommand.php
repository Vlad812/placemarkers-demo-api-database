<?php

declare(strict_types=1);

namespace App\Application\Command\UpdatePlacemarker;

use App\Application\Exception\InvalidParameter;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerId;
use App\Domain\ValueObject\PlacemarkerName;
use Webmozart\Assert\Assert;

final readonly class UpdatePlacemarkerCommand
{
    public function __construct(
        public PlacemarkerId $id,
        public PlacemarkerName $name,
        public Description $description,
        public ?string $typeId = null,
        public ?array $tagIds = null,
    ) {
    }

    /**
     * @param string $id
     * @param array $requestData
     * @return self
     */
    public static function createFromRawValues(string $id, array $requestData): self
    {
        Assert::uuid($id);

        if (!array_key_exists('name', $requestData)) {
            throw InvalidParameter::missing('name');
        }

        Assert::string($requestData['name']);

        $description = '';
        if (isset($requestData['description'])) {
            Assert::string($requestData['description']);
            $description = $requestData['description'];
        }

        $typeId = null;
        if (array_key_exists('type_id', $requestData)) {
            $typeId = $requestData['type_id'];
            if ($typeId !== null) {
                Assert::string($typeId);
            }
        }

        $tagIds = null;
        if (array_key_exists('tags', $requestData)) {
            $tags = $requestData['tags'];
            if ($tags !== null) {
                Assert::isArray($tags);
                Assert::allString($tags);
                $tagIds = array_values($tags);
            }
        }

        return new self(
            PlacemarkerId::fromString($id),
            PlacemarkerName::fromString($requestData['name']),
            Description::fromString($description),
            $typeId,
            $tagIds,
        );
    }
}

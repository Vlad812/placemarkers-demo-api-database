<?php

declare(strict_types=1);

namespace App\Application\Command\CreatePlacemarker;

use App\Application\Exception\InvalidParameter;
use App\Domain\Exception\InvalidCoordinatesException;
use App\Domain\Entity\Placemarker;
use App\Domain\ValueObject\Coordinates;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerName;
use App\Domain\ValueObject\UserUuid;
use Webmozart\Assert\Assert;

final readonly class CreatePlacemarkerCommand
{
    public function __construct(
        public UserUuid $userUuid,
        public PlacemarkerName $name,
        public Coordinates $coordinates,
        public Description $description,
        public string $typeId = Placemarker::DEFAULT_TYPE,
        /** @var list<string> */
        public array $tagIds = [],
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     *
     * @throws InvalidParameter
     * @throws InvalidCoordinatesException
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        foreach (['name', 'lat', 'lon'] as $field) {
            if (!array_key_exists($field, $requestData)) {
                throw InvalidParameter::missing($field);
            }
        }

        Assert::string($requestData['name']);
        Assert::numeric($requestData['lat']);
        Assert::numeric($requestData['lon']);
        Assert::uuid($userUuid);

        $description = '';
        if (isset($requestData['description'])) {
            Assert::string($requestData['description']);
            $description = $requestData['description'];
        }

        $typeId = Placemarker::DEFAULT_TYPE;
        if (isset($requestData['type_id'])) {
            Assert::string($requestData['type_id']);
            $typeId = $requestData['type_id'];
        }

        $tagIds = [];
        if (isset($requestData['tags']) && is_array($requestData['tags'])) {
            Assert::allString($requestData['tags']);
            $tagIds = array_values($requestData['tags']);
        }

        return new self(
            UserUuid::fromString($userUuid),
            PlacemarkerName::fromString($requestData['name']),
            Coordinates::fromStrings((string) $requestData['lat'], (string) $requestData['lon']),
            Description::fromString($description),
            $typeId,
            $tagIds,
        );
    }
}

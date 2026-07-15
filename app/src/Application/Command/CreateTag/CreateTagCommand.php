<?php

declare(strict_types=1);

namespace App\Application\Command\CreateTag;

use App\Application\Exception\InvalidParameter;
use App\Domain\ValueObject\Description;
use App\Domain\ValueObject\PlacemarkerType;
use App\Domain\ValueObject\TagName;
use App\Domain\ValueObject\UserUuid;
use Webmozart\Assert\Assert;

final readonly class CreateTagCommand
{
    public function __construct(
        public UserUuid $userUuid,
        public string $typeId,
        public TagName $name,
        public Description $description,
    ) {
    }

    /**
     * @param array<string, mixed> $requestData
     *
     * @throws InvalidParameter
     */
    public static function createFromRawValues(array $requestData, string $userUuid): self
    {
        if (!array_key_exists('name', $requestData)) {
            throw InvalidParameter::missing('name');
        }

        if (!array_key_exists('type_id', $requestData)) {
            throw InvalidParameter::missing('type_id');
        }

        Assert::string($requestData['name']);
        Assert::string($requestData['type_id']);
        Assert::uuid($userUuid);

        $description = '';
        if (isset($requestData['description'])) {
            Assert::string($requestData['description']);
            $description = $requestData['description'];
        }

        return new self(
            UserUuid::fromString($userUuid),
            PlacemarkerType::fromString($requestData['type_id'])->toString(),
            TagName::fromString($requestData['name']),
            Description::fromString($description),
        );
    }
}

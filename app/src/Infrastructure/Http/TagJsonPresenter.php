<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Entity\Tag;

final class TagJsonPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function present(Tag $tag, bool $status = true, string $msg = 'OK'): array
    {
        return [
            'id' => $tag->id()->toString(),
            'type_id' => $tag->typeId(),
            'name' => $tag->name()->toString(),
            'description' => $tag->description()->toString(),
            'status' => $status,
            'msg' => $msg,
        ];
    }
}

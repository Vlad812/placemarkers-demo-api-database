<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Entity\Placemarker;

final class PlacemarkerJsonPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function present(Placemarker $placemarker, bool $status = true, string $msg = 'OK'): array
    {
        return [
            'id' => $placemarker->id()->toString(),
            'name' => $placemarker->name()->toString(),
            'lat' => sprintf('%.8f', $placemarker->coordinates()->latitude()),
            'lon' => sprintf('%.8f', $placemarker->coordinates()->longitude()),
            'type_id' => $placemarker->typeId(),
            'tags' => $placemarker->tagIds(),
            'description' => $placemarker->description()->toString(),
            'status' => $status,
            'msg' => $msg,
        ];
    }
}

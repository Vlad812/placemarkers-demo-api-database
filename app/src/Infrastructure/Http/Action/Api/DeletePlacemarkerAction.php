<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Api;

use App\Application\Command\DeletePlacemarker\DeletePlacemarkerCommand;
use App\Application\Command\DeletePlacemarker\DeletePlacemarkerHandler;
use App\Infrastructure\Http\Action\AbstractAction;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/api/placemarkers/{id}',
    name: 'api_placemarker_delete',
    methods: ['DELETE'],
)]
final class DeletePlacemarkerAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly DeletePlacemarkerHandler $handler,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $command = DeletePlacemarkerCommand::createFromRawValues(
            (string) $request->attributes->get('id'),
        );

        ($this->handler)($command);

        return $this->respondJson([
            'status' => true,
            'msg' => 'Метка удалена',
        ]);
    }
}

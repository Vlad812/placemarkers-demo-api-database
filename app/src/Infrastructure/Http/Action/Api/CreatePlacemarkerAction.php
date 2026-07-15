<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Api;

use App\Application\Command\CreatePlacemarker\CreatePlacemarkerCommand;
use App\Application\Command\CreatePlacemarker\CreatePlacemarkerHandler;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use App\Infrastructure\Http\Action\AbstractAction;
use App\Infrastructure\Http\PlacemarkerJsonPresenter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/api/placemarkers',
    name: 'api_placemarker_create',
    methods: ['POST'],
)]
final class CreatePlacemarkerAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly CreatePlacemarkerHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $command = CreatePlacemarkerCommand::createFromRawValues(
            $this->getBody($request),
            $this->userProvider->getUserUuid(),
        );
        $placemarker = ($this->handler)($command);

        return $this->respondJson(
            PlacemarkerJsonPresenter::present($placemarker, true, 'Метка сохранена'),
            Response::HTTP_CREATED,
        );
    }
}

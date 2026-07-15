<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Api;

use App\Application\Command\UpdatePlacemarker\UpdatePlacemarkerCommand;
use App\Application\Command\UpdatePlacemarker\UpdatePlacemarkerHandler;
use App\Infrastructure\Http\Action\AbstractAction;
use App\Infrastructure\Http\PlacemarkerJsonPresenter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/api/placemarkers/{id}',
    name: 'api_placemarker_update',
    methods: ['PUT'],
)]
final class UpdatePlacemarkerAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly UpdatePlacemarkerHandler $handler,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $command = UpdatePlacemarkerCommand::createFromRawValues(
            (string) $request->attributes->get('id'),
            $this->getBody($request),
        );
        $placemarker = ($this->handler)($command);

        return $this->respondJson(
            PlacemarkerJsonPresenter::present($placemarker, true, 'Метка обновлена'),
        );
    }
}

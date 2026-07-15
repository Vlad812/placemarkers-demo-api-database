<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Action\Api;

use App\Application\Command\CreateTag\CreateTagCommand;
use App\Application\Command\CreateTag\CreateTagHandler;
use App\Infrastructure\Security\AuthenticatedUserProvider;
use App\Infrastructure\Http\Action\AbstractAction;
use App\Infrastructure\Http\TagJsonPresenter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/api/tags',
    name: 'api_tag_create',
    methods: ['POST'],
)]
final class CreateTagAction extends AbstractAction
{
    public function __construct(
        LoggerInterface $logger,
        private readonly CreateTagHandler $handler,
        private readonly AuthenticatedUserProvider $userProvider,
    ) {
        parent::__construct($logger);
    }

    protected function handleRequest(Request $request): Response
    {
        $command = CreateTagCommand::createFromRawValues(
            $this->getBody($request),
            $this->userProvider->getUserUuid(),
        );
        $tag = ($this->handler)($command);

        return $this->respondJson(
            TagJsonPresenter::present($tag, true, 'Tag created successfully'),
            Response::HTTP_CREATED
        );
    }
}

<?php

namespace Controllers;

use Zephyrus\Application\Controller as BaseController;
use Zephyrus\Network\Response;
use Models\Domain\Services\UserTokenService;
use Models\Domain\Entities\UserToken;

abstract class Controller extends BaseController
{
    private ?UserTokenService $tokenService = null;
    protected ?UserToken $authenticatedUserToken = null;

    public function before(): ?Response
    {
        if (!$this->tokenService) {
            $this->tokenService = new UserTokenService();
        }

        $token = $this->request->getArgument("token");

        if (!$token) {
            return $this->abortUnauthorized("Token manquant.");
        }

        $this->authenticatedUserToken = $this->renewUserToken($token);

        if (!$this->authenticatedUserToken) {
            return $this->abortUnauthorized("Token invalide ou expiré.");
        }

        return parent::before();
    }

    private function renewUserToken(string $tokenValue): ?UserToken
    {
        $userToken = $this->tokenService->renewUserToken($tokenValue);
        return $userToken ? UserToken::mapToToken($userToken) : null;
    }
}

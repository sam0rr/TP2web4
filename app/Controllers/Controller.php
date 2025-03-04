<?php

namespace Controllers;

use Zephyrus\Application\Controller as BaseController;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Models\Domain\Services\UserTokenService;
use Models\Domain\Entities\UserToken;

abstract class Controller extends BaseController
{
    private ?UserTokenService $tokenService = null;
    protected ?UserToken $authenticatedUserToken = null;
    protected ?string $originalToken = null;

    public function before(): ?Response
    {
        if (!$this->tokenService) {
            $this->tokenService = new UserTokenService();
        }

        $this->originalToken = $this->request->getArgument("token");

        if (!$this->originalToken) {
            return $this->abortUnauthorized("Token manquant.");
        }

        $this->authenticatedUserToken = $this->tokenService->validateToken($this->originalToken);

        if (!$this->authenticatedUserToken) {
            return $this->abortUnauthorized("Token ou méthode HTTP invalide.");
        }

        return parent::before();
    }

    public function after(?Response $response): ?Response
    {
        if ($this->authenticatedUserToken) {
            $newToken = $this->tokenService->renewUserTokenByTokenValue($this->originalToken);

            if ($newToken && $response && $response->getContentType() === ContentType::JSON) {
                $content = json_decode($response->getContent(), true) ?? [];
                $content["T O K E N"] = $newToken->token;

                return $this->json($content);
            }
        }

        return parent::after($response);
    }


}

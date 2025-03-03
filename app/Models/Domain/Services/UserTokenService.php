<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Entities\UserToken;
use Models\Domain\Entities\UserProfile;

class UserTokenService
{
    private UserTokenBroker $tokenBroker;

    public function __construct()
    {
        $this->tokenBroker = new UserTokenBroker();
    }

    public function createToken(UserProfile $user): UserToken
    {
        return $this->generateToken($user->id);
    }

    public function renewUserToken(string $oldTokenValue): ?UserToken
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($oldTokenValue);

        if (!$tokenData || !isset($tokenData->userId)) {
            return null;
        }

        $this->tokenBroker->revokeToken($oldTokenValue);
        return $this->generateToken($tokenData->userId);
    }

    private function generateToken(int $userId): UserToken
    {
        $tokenValue = "jwt_" . bin2hex(random_bytes(16));

        $userToken = new UserToken();
        $userToken->userId = $userId;
        $userToken->token = $tokenValue;
        $userToken->createdAt = (new \DateTime())->format("Y-m-d H:i:s");

        $userToken->id = $this->tokenBroker->save($userToken);

        return $userToken;
    }
}

<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Entities\UserToken;
use Models\Domain\Entities\UserProfile;

class UserTokenService
{
    private UserTokenBroker $tokenBroker;
    private UserProfileBroker $userProfileBroker;

    public function __construct()
    {
        $this->tokenBroker = new UserTokenBroker();
        $this->userProfileBroker = new UserProfileBroker();
    }

    public function createToken(UserProfile $user): ?UserToken
    {
        return $this->generateToken($user->id);
    }

    public function renewUserTokenByTokenValue(string $oldTokenValue): ?UserToken
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($oldTokenValue);
        if (!$tokenData) {
            return null;
        }

        if (!$this->tokenBroker->revokeToken($tokenData->userId)) {
            return null;
        }

        return $this->generateToken($tokenData->userId);
    }

    public function renewUserTokenByUserId(int $userId): ?UserToken
    {
        $user = $this->userProfileBroker->findById($userId);
        if (!$user) {
            return null;
        }

        $tokenData = $this->tokenBroker->findValidTokenByUserId($userId);

        if ($tokenData && !$this->tokenBroker->revokeToken($tokenData->userId)) {
            return null;
        }

        return $this->generateToken($userId);
    }

    public function validateToken(string $tokenValue): ?UserToken
    {
        return $this->tokenBroker->findValidTokenByValue($tokenValue) ?: null;
    }

    private function generateToken(int $userId): ?UserToken
    {
        $user = $this->userProfileBroker->findById($userId);
        if (!$user) {
            return null;
        }

        $tokenValue = "jwt_" . uniqid(bin2hex(random_bytes(16)), true);
        $userToken = new UserToken();
        $userToken->userId = $userId;
        $userToken->token = $tokenValue;
        $userToken->createdAt = (new \DateTime())->format("Y-m-d H:i:s");

        return $this->tokenBroker->save($userToken);
    }
}

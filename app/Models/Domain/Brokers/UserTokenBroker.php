<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserToken;

class UserTokenBroker extends DatabaseBroker
{
    public function save(UserToken $token): int
    {
        $this->selectSingle("
            INSERT INTO userToken (userId, token) 
            VALUES (?, ?)", [
                $token->userId,
                $token->token
            ]
        );

        return (int) $this->getDatabase()->getLastInsertedId("authTokens_id_seq");
    }

    public function findValidTokenByValue(string $tokenValue): ?UserToken
    {
        $row = $this->selectSingle("
        SELECT id, userId, token, createdAt
        FROM userToken
        WHERE token = ?
        LIMIT 1",
            [$tokenValue]
        );

        if (!$row) {
            return null;
        }

        return UserToken::mapToToken($row);
    }

    public function revokeToken(string $tokenValue): bool
    {
        $tokenData = $this->findValidTokenByValue($tokenValue);

        if (!$tokenData) {
            return false;
        }

        $this->selectSingle("DELETE FROM userToken WHERE token = ?", [$tokenValue]);

        return true;
    }
}

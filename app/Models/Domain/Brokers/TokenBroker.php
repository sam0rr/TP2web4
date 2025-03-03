<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\Token;

class TokenBroker extends DatabaseBroker
{
    public function save(Token $token): int
    {
        $this->selectSingle("
            INSERT INTO authTokens (userId, token) 
            VALUES (?, ?)", [
                $token->userId,
                $token->token
            ]
        );

        return (int) $this->getDatabase()->getLastInsertedId("authTokens_id_seq");
    }

    public function findValidTokenByValue(string $tokenValue): ?Token
    {
        $row = $this->selectSingle("
        SELECT id, userId, token, createdAt
        FROM authTokens
        WHERE token = ?
        LIMIT 1",
            [$tokenValue]
        );

        if (!$row) {
            return null;
        }

        return Token::mapToToken($row);
    }

    public function revokeToken(string $tokenValue): bool
    {
        $tokenData = $this->findValidTokenByValue($tokenValue);

        if (!$tokenData) {
            return false;
        }

        $this->selectSingle("DELETE FROM authTokens WHERE token = ?", [$tokenValue]);

        return true;
    }
}

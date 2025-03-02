<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\Token;

class TokenBroker extends DatabaseBroker
{
    public function save(Token $token): int
    {
        $this->rawQuery("
            INSERT INTO authTokens (userId, token, createdAt, expiresAt) 
            VALUES (?, ?, ?, ?)",
            [$token->userId, $token->token, $token->createdAt, $token->expiresAt]
        );

        return (int) $this->getDatabase()->getLastInsertedId("authTokens_id_seq");
    }

    public function findValidToken(int $userId): ?Token
    {
        $row = $this->selectSingle("
            SELECT id, userId, token, createdAt, expiresAt 
            FROM authTokens
            WHERE userId = ? AND expiresAt > NOW() 
            ORDER BY createdAt DESC 
            LIMIT 1",
            [$userId]
        );

        if (!$row) {
            return null;
        }

        return $this->mapToToken($row);
    }

    public function revokeToken(int $userId): void
    {
        $this->rawQuery("
            DELETE FROM authTokens WHERE userId = ?",
            [$userId]
        );
    }

    private function mapToToken(object $row): Token
    {
        $token = new Token();
        $token->id = $row->id;
        $token->userId = $row->userId;
        $token->token = $row->token;
        $token->createdAt = $row->createdAt;
        $token->expiresAt = $row->expiresAt;

        return $token;
    }
}

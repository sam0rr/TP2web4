<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserToken;

class UserTokenBroker extends DatabaseBroker
{
    public function save(UserToken $token): ?UserToken
    {

        if (empty($token->token)) {
            return null;
        }

        $this->query("
        INSERT INTO usertoken (userid, token, createdat) 
        VALUES (?, ?, NOW())", [
            $token->userId,
            $token->token
        ]);

        if (!$token->userId) {
            return null;
        }

        return $this->findValidTokenByUserId($token->userId);
    }

    public function findValidTokenByValue(string $tokenValue): ?UserToken
    {
        $row = $this->selectSingle("
        SELECT userid, token, createdat
        FROM usertoken
        WHERE token = ? 
        LIMIT 1",
            [$tokenValue]
        );

        return $row ? UserToken::mapToToken($row) : null;
    }

    public function findValidTokenByUserId(int $userId): ?UserToken
    {
        $row = $this->selectSingle("
        SELECT userid, token, createdat
        FROM usertoken
        WHERE userid = ?
        ORDER BY createdat DESC
        LIMIT 1",
            [$userId]
        );

        return $row ? UserToken::mapToToken($row) : null;
    }

    public function revokeToken(int $userId): bool
    {
        try {
            $this->query("DELETE FROM usertoken WHERE userid = ?", [$userId]);
            return true;
        } catch (\Exception $e) {
            error_log("Failed to revoke token userID $userId: " . $e->getMessage());
            return false;
        }
    }
}

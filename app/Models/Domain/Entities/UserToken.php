<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class UserToken extends Entity
{
    public int $id;
    public int $userId;
    public string $token;
    public string $createdAt;

    public static function mapToToken(object $row): UserToken
    {
        $token = new UserToken();
        $token->id = $row->id;
        $token->userId = $row->userid;
        $token->token = $row->token;
        $token->createdAt = $row->createdat;

        return $token;
    }

}

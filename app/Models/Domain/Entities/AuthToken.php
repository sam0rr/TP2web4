<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class AuthToken extends Entity
{
    public int $id;
    public int $userId;
    public string $token;
    public string $createdAt;
    public string $expiresAt;
}

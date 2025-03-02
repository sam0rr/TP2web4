<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class UserWallet extends Entity
{
    public int $userId;
    public float $balance;
}

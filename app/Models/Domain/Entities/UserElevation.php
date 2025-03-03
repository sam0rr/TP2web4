<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class UserElevation extends Entity
{
    public int $userId;
    public float $totalSpent;
    public string $lastChecked;
}
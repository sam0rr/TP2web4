<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class UserWallet extends Entity
{
    public int $userId;
    public float $balance;
    public float $totalSpent;

    public static function mapToUserWallet(object $row): UserWallet
    {
        $wallet = new UserWallet();
        $wallet->userId = $row->userid;
        $wallet->balance = $row->balance;
        $wallet->totalSpent = $row->totalspent;

        return $wallet;
    }
}


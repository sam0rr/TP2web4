<?php

namespace Models\Domain\Brokers;

use Models\Domain\Entities\UserWallet;
use Zephyrus\Database\DatabaseBroker;

class UserWalletBroker extends DatabaseBroker
{
    public function findOrCreateWallet(int $userId): UserWallet
    {
        $wallet = $this->findByUserId($userId);

        if (!$wallet) {
            $this->query("
            INSERT INTO userWallet (userId, balance, totalSpent) 
            VALUES (?, 0, 0)", [$userId]);

            $wallet = $this->findByUserId($userId);
        }

        return $wallet;
    }

    public function addFunds(int $userId, float $amount): ?UserWallet
    {
        $this->query("
            UPDATE userWallet
            SET balance = balance + ?
            WHERE userId = ?",
            [$amount, $userId]
        );

        return $this->findByUserId($userId);
    }

    public function withdrawFunds(int $userId, float $amount): ?UserWallet
    {
        $this->query("
            UPDATE userWallet
            SET balance = balance - ?
            WHERE userId = ?",
            [$amount, $userId]
        );

        return $this->findByUserId($userId);
    }

    public function updateTotalSpent(int $userId, float $amount): ?UserWallet
    {
        $this->query("
        UPDATE userWallet 
        SET totalSpent = totalSpent + ? 
        WHERE userId = ?",
            [$amount, $userId]
        );

        return $this->findByUserId($userId);
    }

    public function findByUserId(int $userId): ?UserWallet
    {
        $row = $this->selectSingle("
            SELECT userId, balance, totalSpent
            FROM userWallet
            WHERE userId = ?",
            [$userId]
        );

        return $row ? UserWallet::mapToUserWallet($row) : null;
    }
}

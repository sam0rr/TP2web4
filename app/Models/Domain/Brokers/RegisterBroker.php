<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserProfile;

class RegisterBroker extends DatabaseBroker
{
    public function registerUser(UserProfile $user): ?UserProfile
    {
        $row = $this->query("
        INSERT INTO userProfile (username, firstname, lastname, email, password, type) 
        VALUES (?, ?, ?, ?, ?, 'NORMAL') RETURNING id", [
                $user->username,
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->password
            ]
        );

        if (!$row || !isset($row->id)) {
            return null;
        }

        $user->id = $row->id;

        try {
            $this->query("
            INSERT INTO userWallet (userId, balance, totalSpent) 
            VALUES (?, 0, 0)", [$user->id]
            );
        } catch (\Exception) {
            return null;
        }

        return $this->findUserById($user->id);
    }

    public function findUserById(int $id): ?UserProfile
    {
        $row = $this->selectSingle("
        SELECT id, username, firstname, lastname, email, password, type
        FROM userProfile
        WHERE id = ?",
            [$id]
        );

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function usernameExists(string $username): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfile WHERE username = ?", [$username]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfile WHERE email = ?", [$email]);
    }
}

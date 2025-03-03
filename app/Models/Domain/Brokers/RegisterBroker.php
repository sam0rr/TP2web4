<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserProfile;

class RegisterBroker extends DatabaseBroker
{
    public function registerUser(UserProfile $user): ?UserProfile
    {
        $this->query("
            INSERT INTO userProfile (username, firstname, lastname, email, password, type) 
            VALUES (?, ?, ?, ?, ?, 'NORMAL')", [
                $user->username,
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->password
            ]
        );

        $user->id = (int) $this->getDatabase()->getLastInsertedId("userProfile_id_seq");

        if (!$user->id) {
            return null;
        }

        try {
            $this->query("
                INSERT INTO userWallet (userId, balance, totalSpent) 
                VALUES (?, 0, 0)", [$user->id]
            );
        } catch (\Exception) {
            return null;
        }

        return $user;
    }

    public function usernameExists(string $username): bool
    {
        return (bool) $this->selectSingle()("SELECT 1 FROM userProfile WHERE username = ?", [$username]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfile WHERE email = ?", [$email]);
    }
}

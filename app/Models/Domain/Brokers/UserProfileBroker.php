<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;

class UserProfileBroker extends DatabaseBroker
{
    public function usernameExists(string $username): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfiles WHERE username = ?", [$username]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfiles WHERE email = ?", [$email]);
    }

    public function registerUser(array $data): int
    {
        $this->rawQuery("
        INSERT INTO userProfiles (username, firstname, lastname, email, password, type) 
        VALUES (?, ?, ?, ?, ?, 'NORMAL')",
            [$data['username'], $data['firstname'], $data['lastname'], $data['email'], $data['password']]
        );
        return (int) $this->getDatabase()->getLastInsertedId("userprofiles_id_seq");
    }
}

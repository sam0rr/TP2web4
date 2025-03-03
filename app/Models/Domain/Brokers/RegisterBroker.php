<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserProfile;

class RegisterBroker extends DatabaseBroker
{
    public function registerUser(UserProfile $user): UserProfile
    {
        $this->rawQuery("
        INSERT INTO userProfile (username, firstname, lastname, email, password, type) 
        VALUES (?, ?, ?, ?, ?, 'NORMAL')", [
                $user->username,
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->password
            ]
        );

        $user->id = (int) $this->getDatabase()->getLastInsertedId("userprofiles_id_seq");

        return $user;
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

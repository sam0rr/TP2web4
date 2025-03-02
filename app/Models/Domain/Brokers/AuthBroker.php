<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\UserProfile;

class AuthBroker extends DatabaseBroker
{
    public function usernameExists(string $username): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfiles WHERE username = ?", [$username]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->selectSingle("SELECT 1 FROM userProfiles WHERE email = ?", [$email]);
    }

    public function findByUsername(string $username): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
            FROM userProfiles 
            WHERE username = ?",
            [$username]
        );

        return $row ? $this->mapToUserProfile($row) : null;
    }

    public function findById(int $userId): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
        FROM userProfiles 
        WHERE id = ?",
            [$userId]
        );

        return $row ? $this->mapToUserProfile($row) : null;
    }

    public function registerUser(UserProfile $user): UserProfile
    {
        $this->rawQuery("
        INSERT INTO userProfiles (username, firstname, lastname, email, password, type) 
        VALUES (?, ?, ?, ?, ?, 'NORMAL')",
            [$user->username, $user->firstname, $user->lastname, $user->email, $user->password]
        );

        $user->id = (int) $this->getDatabase()->getLastInsertedId("userprofiles_id_seq");

        return $user;
    }

    private function mapToUserProfile(object $row): UserProfile
    {
        $user = new UserProfile();
        $user->id = $row->id;
        $user->username = $row->username;
        $user->firstname = $row->firstname;
        $user->lastname = $row->lastname;
        $user->email = $row->email;
        $user->password = $row->password;
        $user->type = $row->type;

        return $user;
    }
}

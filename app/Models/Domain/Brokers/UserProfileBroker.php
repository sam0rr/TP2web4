<?php

namespace Models\Domain\Brokers;

use Models\Domain\Entities\UserProfile;
use Zephyrus\Database\DatabaseBroker;

class UserProfileBroker extends DatabaseBroker
{
    public function updateUserProfile(int $userId, array $data): ?UserProfile
    {
        if (empty($data)) {
            return null;
        }

        $setClauses = [];
        $values = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $userId;

        $sql = "UPDATE userProfiles SET " . implode(", ", $setClauses) . " WHERE id = ?";

        $this->rawQuery($sql, $values);

        $row = $this->selectSingle("SELECT * FROM userProfiles WHERE id = ?", [$userId]);

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function findById(int $userId): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
        FROM userProfiles 
        WHERE id = ?",
            [$userId]
        );

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function findByUsername(string $username): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
            FROM userProfiles 
            WHERE username = ?",
            [$username]
        );

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }







}

<?php

namespace Models\Domain\Brokers;

use Models\Domain\Entities\UserProfile;
use Zephyrus\Database\DatabaseBroker;
use Zephyrus\Security\Cryptography;

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

        $sql = "UPDATE userProfile SET " . implode(", ", $setClauses) . " WHERE id = ?";

        $this->rawQuery($sql, $values);

        $row = $this->selectSingle("SELECT * FROM userProfile WHERE id = ?", [$userId]);

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function updatePassword(int $userId, string $newPassword): ?UserProfile
    {
        $hashedPassword = Cryptography::hashPassword($newPassword);

        $this->rawQuery("
        UPDATE userProfile
        SET password = ?
        WHERE id = ?",
            [$hashedPassword, $userId]
        );

        $row = $this->selectSingle("SELECT * FROM userProfile WHERE id = ?", [$userId]);

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function updateAccountType(int $userId, string $newType): ?UserProfile
    {
        $this->rawQuery("
        UPDATE userProfile 
        SET type = ?
        WHERE id = ?",
            [$newType, $userId]
        );
        $row = $this->selectSingle("SELECT * FROM userProfile WHERE id = ?", [$userId]);

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function findById(int $userId): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
        FROM userProfile
        WHERE id = ?",
            [$userId]
        );

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }

    public function findByUsername(string $username): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
            FROM userProfile 
            WHERE username = ?",
            [$username]
        );

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }
}

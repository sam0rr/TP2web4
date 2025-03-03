<?php

namespace Models\Domain\Brokers;

use Models\Domain\Entities\UserProfile;
use Zephyrus\Database\DatabaseBroker;
use Zephyrus\Security\Cryptography;

class UserProfileBroker extends DatabaseBroker
{
    private array $allowedFields = ['username', 'firstname', 'lastname', 'email'];

    public function updateUserProfile(int $userId, array $data): ?UserProfile
    {
        if (empty($data)) {
            return null;
        }

        $setClauses = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, $this->allowedFields, true)) {
                continue;
            }
            $setClauses[] = "$key = ?";
            $values[] = $value;
        }

        if (empty($setClauses)) {
            return null;
        }

        $values[] = $userId;

        $sql = "UPDATE userProfile SET " . implode(", ", $setClauses) . " WHERE id = ?";
        $this->query($sql, $values);

        return $this->findById($userId);
    }

    public function updatePassword(int $userId, string $newPassword): ?UserProfile
    {
        $hashedPassword = Cryptography::hashPassword($newPassword);

        $this->query("
        UPDATE userProfile
        SET password = ?
        WHERE id = ?", [$hashedPassword, $userId]);

        return $this->findById($userId);
    }

    public function updateAccountType(int $userId, string $newType): ?UserProfile
    {
        $allowedTypes = ['NORMAL', 'PREMIUM'];
        if (!in_array($newType, $allowedTypes, true)) {
            return null;
        }

        $this->query("
        UPDATE userProfile 
        SET type = ?
        WHERE id = ?", [$newType, $userId]);

        return $this->findById($userId);
    }

    public function findById(int $userId): ?UserProfile
    {
        $row = $this->selectSingle(
            "SELECT id, username, firstname, lastname, email, password, type 
            FROM userProfile
            WHERE id = ?", [$userId]);

        return $row ? UserProfile::mapToUserProfile($row) : null;
    }
}

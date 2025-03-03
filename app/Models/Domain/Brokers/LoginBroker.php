<?php

namespace Models\Domain\Brokers;

use Models\Domain\Entities\UserProfile;
use Zephyrus\Database\DatabaseBroker;

class LoginBroker extends DatabaseBroker
{
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
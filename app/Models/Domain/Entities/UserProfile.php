<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class UserProfile extends Entity
{
    public int $id;
    public string $username;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;
    public string $type;

    public static function mapToUserProfile(object $row): UserProfile
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

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
}

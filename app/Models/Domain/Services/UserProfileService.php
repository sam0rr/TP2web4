<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Validators\UserProfileValidator;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class UserProfileService
{
    public function registerUser(Form $form): array
    {
        UserProfileValidator::assert($form);
        $broker = new UserProfileBroker();

        if ($broker->usernameExists($form->getValue('username'))) {
            return ["error" => "Username utilisé", "status" => 400];
        }
        if ($broker->emailExists($form->getValue('email'))) {
            return ["error" => "Email utilisé", "status" => 400];
        }

        $hashedPassword = Cryptography::hashPassword($form->getValue('password'));

        $userId = $broker->registerUser([
            'username' => $form->getValue('username'),
            'firstname' => $form->getValue('firstname'),
            'lastname' => $form->getValue('lastname'),
            'email' => $form->getValue('email'),
            'password' => $hashedPassword,
        ]);

        return [
            "message" => "User enregistré avec succès",
            "userId" => $userId,
            "status" => 201
        ];
    }
}
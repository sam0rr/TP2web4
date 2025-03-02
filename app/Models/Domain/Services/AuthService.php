<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\AuthBroker;
use Models\Domain\Validators\AuthValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class AuthService
{
    public function registerUser(Form $form): array
    {
        try {
            AuthValidator::assert($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $broker = new AuthBroker();

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
            "userId" => $userId
        ];
    }
}

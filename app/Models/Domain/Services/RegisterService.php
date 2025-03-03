<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\RegisterBroker;
use Models\Domain\Brokers\TokenBroker;
use Models\Domain\Entities\Token;
use Models\Domain\Entities\UserProfile;
use Models\Domain\Validators\RegisterValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class RegisterService
{
    private RegisterBroker $broker;
    private TokenBroker $tokenBroker;

    public function __construct()
    {
        $this->broker = new RegisterBroker();
        $this->tokenBroker = new TokenBroker();
    }

    public function registerUser(Form $form): array
    {
        try {
            RegisterValidator::assertRegister($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        if ($this->broker->usernameExists($form->getValue('username'))) {
            return ["errors" => ["Username utilisé"], "status" => 400];
        }
        if ($this->broker->emailExists($form->getValue('email'))) {
            return ["errors" => ["Email utilisé"], "status" => 400];
        }

        $hashedPassword = Cryptography::hashPassword($form->getValue('password'));

        $user = new UserProfile();
        $user->username = $form->getValue('username');
        $user->firstname = $form->getValue('firstname');
        $user->lastname = $form->getValue('lastname');
        $user->email = $form->getValue('email');
        $user->password = $hashedPassword;
        $user->type = 'NORMAL';

        $user = $this->broker->registerUser($user);

        $token = $this->createToken($user);

        return [
            "message" => "User enregistré avec succès",
            "token" => $token->token,
            "user" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "type" => $user->type
            ]
        ];
    }

    private function createToken(UserProfile $user): Token
    {
        $tokenValue = "jwt_{$user->username}_" . bin2hex(random_bytes(16));

        $token = new Token();
        $token->userId = $user->id;
        $token->token = $tokenValue;
        $token->createdAt = (new \DateTime())->format("Y-m-d H:i:s");

        $token->id = $this->tokenBroker->save($token);

        return $token;
    }
}

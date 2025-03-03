<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\RegisterBroker;
use Models\Domain\Brokers\userTokenBroker;
use Models\Domain\Entities\UserToken;
use Models\Domain\Entities\UserProfile;
use Models\Domain\Validators\RegisterValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class RegisterService
{
    private RegisterBroker $broker;
    private UserTokenBroker $userTokenBroker;

    public function __construct()
    {
        $this->broker = new RegisterBroker();
        $this->userTokenBroker = new UserTokenBroker();
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

    private function createToken(UserProfile $user): UserToken
    {
        $tokenValue = "jwt_{$user->username}_" . bin2hex(random_bytes(16));

        $userToken = new UserToken();
        $userToken->userId = $user->id;
        $userToken->token = $tokenValue;
        $userToken->createdAt = (new \DateTime())->format("Y-m-d H:i:s");

        $userToken->id = $this->userTokenBroker->save($userToken);

        return $userToken;
    }
}

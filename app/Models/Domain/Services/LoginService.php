<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\LoginBroker;
use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Validators\LoginValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class LoginService
{
    private LoginBroker $loginBroker;
    private userTokenBroker $tokenBroker;

    public function __construct()
    {
        $this->loginBroker = new LoginBroker();
        $this->tokenBroker = new userTokenBroker();
    }

    public function authenticateUser(Form $form): array
    {
        try {
            LoginValidator::assertLogin($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $username = $form->getValue("username");
        $password = $form->getValue("password");

        $user = $this->loginBroker->findByUsername($username);

        if (!$user) {
            return ["errors" => ["Champs incorrects."], "status" => 401];
        }

        if (!Cryptography::verifyHashedPassword($password, $user->password)) {
            return ["errors" => ["Mot de passe incorrect."], "status" => 401];
        }

        $tokenData = $this->tokenBroker->findValidTokenByUserId($user->id);

        return [
            "message" => "Connexion réussie",
            "userToken" => $tokenData->token
        ];
    }
}
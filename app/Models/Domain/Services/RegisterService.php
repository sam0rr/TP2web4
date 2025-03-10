<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\RegisterBroker;
use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Validators\RegisterValidator;
use Models\Domain\Entities\UserProfile;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class RegisterService
{
    private RegisterBroker $broker;
    private UserTokenService $userTokenService;
    private UserProfileBroker $userProfileBroker;

    public function __construct()
    {
        $this->broker = new RegisterBroker();
        $this->userTokenService = new UserTokenService();
        $this->userProfileBroker = new UserProfileBroker();
    }

    public function registerUser(Form $form): array
    {
        try {
            RegisterValidator::assertRegister($form, $this->userProfileBroker);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
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
        $token = $this->userTokenService->createToken($user);

        return [
            "message" => "User enregistré avec succès",
            "T O K E N" => $token->token,
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
}

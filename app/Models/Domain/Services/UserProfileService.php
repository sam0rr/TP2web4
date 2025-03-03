<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Brokers\userTokenBroker;
use Models\Domain\Validators\UserProfileValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class UserProfileService
{
    private UserProfileBroker $userProfileBroker;
    private userTokenBroker $tokenBroker;

    public function __construct()
    {
        $this->userProfileBroker = new UserProfileBroker();
        $this->tokenBroker = new userTokenBroker();
    }

    public function authenticateUser(Form $form): array
    {
        try {
            UserProfileValidator::assertLogin($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $username = $form->getValue("username");
        $password = $form->getValue("password");

        $user = $this->userProfileBroker->findByUsername($username);

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
    public function updateUserProfile(string $token, Form $form): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        $userId = $tokenData->userId;

        $data = array_filter($form->getFields(), function ($value, $key) {
            return !is_null($value) && $value !== "" && $key !== "password" && $key !== "type";
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($data)) {
            return ["errors" => ["Aucune donnée à mettre à jour"], "status" => 400];
        }

        try {
            UserProfileValidator::assertUpdate(new Form($data));
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        unset($data['password'], $data['type']);

        $updatedUser = $this->userProfileBroker->updateUserProfile($userId, $data);

        if (!$updatedUser) {
            return ["errors" => ["Erreur lors de la mise à jour du profil"], "status" => 500];
        }

        return [
            "message" => "Profil mis à jour avec succès",
            "user" => [
                "id" => $updatedUser->id,
                "username" => $updatedUser->username,
                "email" => $updatedUser->email,
                "firstname" => $updatedUser->firstname,
                "lastname" => $updatedUser->lastname,
                "type" => $updatedUser->type
            ],
            "status" => 200
        ];
    }

    public function updatePassword(string $token, Form $form): array
    {
        try {
            UserProfileValidator::assertPasswordUpdate($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        $user = $this->userProfileBroker->findById($tokenData->userId);

        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        $oldPassword = $form->getValue("old_password");
        $newPassword = $form->getValue("new_password");

        if (!Cryptography::verifyHashedPassword($oldPassword, $user->password)) {
            return ["errors" => ["Ancien mot de passe incorrect."], "status" => 400];
        }

        $updatedUser = $this->userProfileBroker->updatePassword($user->id, $newPassword);

        if (!$updatedUser) {
            return ["errors" => ["Erreur lors de la mise à jour du mot de passe"], "status" => 500];
        }

        return [
            "message" => "Mot de passe mis à jour avec succès",
            "status" => 200
        ];
    }

    public function elevateAccount(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        $user = $this->userProfileBroker->findById($tokenData->userId);

        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        if ($user->type === "PREMIUM") {
            return ["errors" => ["L'utilisateur est déjà PREMIUM."], "status" => 400];
        }

        $updatedUser = $this->userProfileBroker->updateAccountType($user->id, "PREMIUM");

        return [
            "message" => "Compte mis à niveau vers PREMIUM avec succès",
            "user" => [
                "username" => $updatedUser->username,
                "type" => $updatedUser->type
            ],
            "status" => 200
        ];
    }

    public function getUserProfile(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        if (!$tokenData) {
            return ["errors" => ["Token invalide"], "status" => 401];
        }

        $user = $this->userProfileBroker->findById($tokenData->userId);

        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        return [
            "id" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "type" => $user->type
        ];
    }
}

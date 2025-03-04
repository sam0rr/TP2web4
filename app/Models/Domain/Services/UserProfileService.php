<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Brokers\UserWalletBroker;
use Models\Domain\Validators\UserProfileValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Security\Cryptography;

class UserProfileService
{
    private UserProfileBroker $userProfileBroker;
    private UserWalletBroker $userWalletBroker;
    private UserTokenBroker $tokenBroker;

    public function __construct()
    {
        $this->userProfileBroker = new UserProfileBroker();
        $this->userWalletBroker = new UserWalletBroker();
        $this->tokenBroker = new UserTokenBroker();
    }

    public function updateUserProfile(string $token, Form $form): array
    {
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $data = array_filter($form->getFields(), function ($value, $key) {
            return !is_null($value) && $value !== "" && $key !== "password" && $key !== "type";
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($data)) {
            return ["errors" => ["Aucune donnée à mettre à jour"], "status" => 400];
        }

        try {
            UserProfileValidator::assertUpdate(new Form($data));
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

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
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        try {
            UserProfileValidator::assertPasswordUpdate($form);
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

        $user = $this->userProfileBroker->findById($userId);
        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        $oldPassword = $form->getValue("oldpassword");
        $newPassword = $form->getValue("newpassword");

        if (!Cryptography::verifyHashedPassword($oldPassword, $user->password)) {
            return ["errors" => ["Ancien mot de passe incorrect."], "status" => 400];
        }

        $updatedUser = $this->userProfileBroker->updatePassword($userId, $newPassword);

        if (!$updatedUser) {
            return ["errors" => ["Erreur lors de la mise à jour du mot de passe"], "status" => 500];
        }

        return ["message" => "Mot de passe mis à jour avec succès", "status" => 200];
    }

    public function elevateAccount(string $token, Form $form): array
    {
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $user = $this->userProfileBroker->findById($userId);
        $wallet = $this->userWalletBroker->findByUserId($userId);

        try {
            UserProfileValidator::assertElevationEligibility($user, $wallet, $form);
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

        $updatedUser = $this->userProfileBroker->updateAccountType($userId, "PREMIUM");
        if (!$updatedUser) {
            return ["errors" => ["Échec de l'élévation du compte"], "status" => 500];
        }

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
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $user = $this->userProfileBroker->findById($userId);
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

    private function getUserIdFromToken(string $token): ?int
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);
        return $tokenData?->userId;
    }
}

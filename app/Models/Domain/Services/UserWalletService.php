<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Brokers\UserWalletBroker;
use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Validators\UserWalletValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;

class UserWalletService
{
    private UserWalletBroker $walletBroker;
    private UserTokenBroker $tokenBroker;
    private UserProfileBroker $profileBroker;

    public function __construct()
    {
        $this->walletBroker = new UserWalletBroker();
        $this->tokenBroker = new UserTokenBroker();
        $this->profileBroker = new UserProfileBroker();
    }

    private function getUserIdFromToken(string $token): ?int
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);
        return $tokenData ? $tokenData->userId : null;
    }

    public function getUserCredits(string $token): array
    {
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $wallet = $this->walletBroker->findOrCreateWallet($userId);
        if (!$wallet) {
            return ["errors" => ["Impossible de récupérer le portefeuille"], "status" => 500];
        }

        return [
            "userId" => $wallet->userId,
            "balance" => $wallet->balance,
            "totalSpent" => $wallet->totalSpent,
            "status" => 200
        ];
    }

    public function addCredits(string $token, float $amount, Form $form): array
    {
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $user = $this->profileBroker->findById($userId);
        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        try {
            UserWalletValidator::assertCreditAmount($user->type, $form);
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

        $wallet = $this->walletBroker->findOrCreateWallet($userId);
        if (!$wallet) {
            return ["errors" => ["Échec de l'accès au portefeuille"], "status" => 500];
        }

        $updatedWallet = $this->walletBroker->addFunds($wallet->userId, $amount);
        if (!$updatedWallet) {
            return ["errors" => ["Erreur lors de l'ajout des crédits"], "status" => 500];
        }

        return [
            "message" => "Crédits ajoutés avec succès.",
            "balance" => $updatedWallet->balance,
            "status" => 200
        ];
    }

    public function withdrawCredits(string $token, float $amount, Form $form): array
    {
        $userId = $this->getUserIdFromToken($token);
        if (!$userId) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 403];
        }

        $user = $this->profileBroker->findById($userId);
        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        $wallet = $this->walletBroker->findOrCreateWallet($userId);
        if (!$wallet) {
            return ["errors" => ["Échec de l'accès au portefeuille"], "status" => 500];
        }

        try {
            UserWalletValidator::assertWithdrawAmount($form, $amount, $wallet->balance);
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

        $updatedWallet = $this->walletBroker->withdrawFunds($wallet->userId, $amount);
        if (!$updatedWallet) {
            return ["errors" => ["Erreur lors du retrait"], "status" => 500];
        }

        return [
            "message" => "Retrait effectué avec succès.",
            "balance" => $updatedWallet->balance,
            "status" => 200
        ];
    }
}

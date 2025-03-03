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

    public function getUserCredits(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        $wallet = $this->walletBroker->findOrCreateWallet($tokenData->userId);

        return [
            "userId" => $wallet->userId,
            "balance" => $wallet->balance,
            "totalSpent" => $wallet->totalSpent,
            "status" => 200
        ];
    }

    public function addCredits(string $token, float $amount, Form $form): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);


        $user = $this->profileBroker->findById($tokenData->userId);

        if (!$user) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }


        try {
            UserWalletValidator::assertCreditAmount($user->type, $form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $wallet = $this->walletBroker->findOrCreateWallet($user->id);
        $updatedWallet = $this->walletBroker->addFunds($wallet->userId, $amount);

        return [
            "message" => "Crédits ajoutés avec succès.",
            "balance" => $updatedWallet->balance,
            "status" => 200
        ];
    }

}


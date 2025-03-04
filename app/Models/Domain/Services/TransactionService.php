<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\TransactionBroker;
use Models\Domain\Brokers\UserProfileBroker;
use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Brokers\UserWalletBroker;
use Models\Domain\Entities\Transaction;
use Models\Domain\Validators\TransactionValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;

class TransactionService
{
    private TransactionBroker $transactionBroker;
    private UserWalletBroker $walletBroker;
    private UserTokenBroker $tokenBroker;
    private UserProfileBroker $profileBroker;

    public function __construct()
    {
        $this->transactionBroker = new TransactionBroker();
        $this->walletBroker = new UserWalletBroker();
        $this->tokenBroker = new UserTokenBroker();
        $this->profileBroker = new UserProfileBroker();
    }

    public function addTransaction(string $token, Form $form): array
    {
        $user = $this->getUserFromToken($token);
        if (isset($user["errors"])) {
            return $user;
        }

        try {
            TransactionValidator::assertTransaction($form, $user["type"]);
        } catch (FormException $e) {
            return ["errors" => array_values($e->getForm()->getErrorMessages()), "status" => 400];
        }

        $totalPrice = (float) $form->getValue("price") * (int) $form->getValue("quantity");

        if (!$this->canAffordTransaction($user["userId"], $totalPrice)) {
            return ["errors" => ["Fonds insuffisants pour effectuer l'achat"], "status" => 400];
        }

        $savedTransaction = $this->saveTransaction($user["userId"], $form, $totalPrice);
        if (!$savedTransaction) {
            return ["errors" => ["Erreur lors de l'ajout de la transaction"], "status" => 500];
        }

        $this->applyTransaction($user["userId"], $totalPrice);

        return [
            "message" => "Transaction ajoutée avec succès",
            "transaction" => [
                "name" => $savedTransaction->itemName,
                "quantity" => $savedTransaction->quantity,
                "price" => $savedTransaction->price,
                "totalPrice" => $savedTransaction->totalPrice,
            ],
            "status" => 201
        ];
    }

    public function getUserTransactions(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        $transactions = $this->transactionBroker->findTransactionsByUserId($tokenData->userId);

        $total = array_reduce($transactions, function ($sum, $transaction) {
            return $sum + ($transaction->price * $transaction->quantity);
        }, 0);

        $formattedTransactions = array_map(function ($transaction) {
            return [
                "name" => $transaction->itemName,
                "price" => $transaction->price,
                "quantity" => $transaction->quantity,
                "totalPrice" => ($transaction->price * $transaction->quantity)
            ];
        }, $transactions);
        
        return [
            "token" => $token,
            "transactions" => $formattedTransactions,
            "totalSpent" => $total
        ];
    }

    private function getUserFromToken(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);
        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        $userProfile = $this->profileBroker->findById($tokenData->userId);
        if (!$userProfile) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        return ["userId" => $tokenData->userId, "type" => $userProfile->type];
    }

    private function canAffordTransaction(int $userId, float $totalPrice): bool
    {
        $wallet = $this->walletBroker->findByUserId($userId);
        return $wallet && $wallet->balance >= $totalPrice;
    }

    private function saveTransaction(int $userId, Form $form, float $totalPrice): ?Transaction
    {
        $transaction = new Transaction();
        $transaction->userId = $userId;
        $transaction->itemName = $form->getValue("name");
        $transaction->price = (float) $form->getValue("price");
        $transaction->quantity = (int) $form->getValue("quantity");
        $transaction->totalPrice = $totalPrice;

        return $this->transactionBroker->save($transaction);
    }

    private function applyTransaction(int $userId, float $totalPrice): void
    {
        $this->walletBroker->withdrawFunds($userId, $totalPrice);
        $this->walletBroker->updateTotalSpent($userId, $totalPrice);
    }
}

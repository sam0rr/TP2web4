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
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);
        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        $userProfile = $this->profileBroker->findById($tokenData->userId);
        if (!$userProfile) {
            return ["errors" => ["Utilisateur non trouvé"], "status" => 404];
        }

        try {
            TransactionValidator::assertTransaction($form, $userProfile->type);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $totalPrice = (float) $form->getValue("price") * (int) $form->getValue("quantity");

        $wallet = $this->walletBroker->findByUserId($tokenData->userId);
        if (!$wallet || $wallet->balance < $totalPrice) {
            return ["errors" => ["Fonds insuffisants pour effectuer l'achat"], "status" => 400];
        }

        $transaction = new Transaction();
        $transaction->userId = $tokenData->userId;
        $transaction->itemName = $form->getValue("item_name");
        $transaction->price = (float) $form->getValue("price");
        $transaction->quantity = (int) $form->getValue("quantity");
        $transaction->totalPrice = $totalPrice;

        $savedTransaction = $this->transactionBroker->save($transaction);

        if (!$savedTransaction) {
            return ["errors" => ["Erreur lors de l'ajout de la transaction"], "status" => 500];
        }

        $this->walletBroker->withdrawFunds($transaction->userId, $transaction->totalPrice);
        $this->walletBroker->updateTotalSpent($transaction->userId, $transaction->totalPrice);

        return [
            "message" => "Transaction ajoutée avec succès",
            "transaction" => [
                "name" => $transaction->itemName,
                "price" => $transaction->price,
                "quantity" => $transaction->quantity
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

        $formattedTransactions = array_map(function ($transaction) {
            return [
                "name" => $transaction->itemName,
                "price" => $transaction->price,
                "quantity" => $transaction->quantity
            ];
        }, $transactions);

        return [
            "token" => $token,
            "transactions" => $formattedTransactions
        ];
    }

}

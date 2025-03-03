<?php

namespace Models\Domain\Services;

use Models\Domain\Brokers\TransactionBroker;
use Models\Domain\Brokers\UserTokenBroker;
use Models\Domain\Entities\Transaction;
use Models\Domain\Validators\TransactionValidator;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;

class TransactionService
{
    private TransactionBroker $transactionBroker;
    private UserTokenBroker $tokenBroker;

    public function __construct()
    {
        $this->transactionBroker = new TransactionBroker();
        $this->tokenBroker = new UserTokenBroker();
    }

    public function addTransaction(string $token, Form $form): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        try {
            TransactionValidator::assertTransaction($form);
        } catch (FormException $e) {
            return [
                "errors" => array_values($e->getForm()->getErrorMessages()),
                "status" => 400
            ];
        }

        $transaction = new Transaction();
        $transaction->userId = $tokenData->userId;
        $transaction->itemName = $form->getValue("item_name");
        $transaction->price = (float) $form->getValue("price");
        $transaction->quantity = (int) $form->getValue("quantity");
        $transaction->totalPrice = $transaction->price * $transaction->quantity;

        $savedTransaction = $this->transactionBroker->save($transaction);

        if (!$savedTransaction) {
            return ["errors" => ["Erreur lors de l'ajout de la transaction"], "status" => 500];
        }

        return [
            "message" => "Transaction ajoutée avec succès",
            "transaction" => $savedTransaction,
            "status" => 201
        ];
    }

    public function getUserTransactions(string $token): array
    {
        $tokenData = $this->tokenBroker->findValidTokenByValue($token);

        if (!$tokenData) {
            return ["errors" => ["Token invalide ou expiré"], "status" => 401];
        }

        return $this->transactionBroker->findTransactionsByUserId($tokenData->userId);
    }
}

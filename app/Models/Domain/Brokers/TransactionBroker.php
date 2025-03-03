<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\Transaction;

class TransactionBroker extends DatabaseBroker
{
    public function save(Transaction $transaction): ?Transaction
    {
        $this->query("
            INSERT INTO transaction (userid, itemname, price, quantity) 
            VALUES (?, ?, ?, ?)", [
                $transaction->userId,
                $transaction->itemName,
                $transaction->price,
                $transaction->quantity,
                $transaction->totalPrice
            ]
        );

        $transaction->id = (int) $this->getDatabase()->getLastInsertedId("transaction_id_seq");

        return $this->findTransactionById($transaction->id);
    }

    public function findTransactionsByUserId(int $userId): array
    {
        $rows = $this->select("
            SELECT id, userid, itemname, price, quantity, totalprice, createdat
            FROM transaction
            WHERE userid = ?
            ORDER BY createdat DESC",
            [$userId]
        );

        return array_map(fn($row) => Transaction::mapToTransaction($row), $rows);
    }

    public function findTransactionById(int $id): ?Transaction
    {
        $row = $this->selectSingle("
            SELECT id, userid, itemname, price, quantity, totalprice, createdat
            FROM transaction
            WHERE id = ?",
            [$id]
        );

        return $row ? Transaction::mapToTransaction($row) : null;
    }
}

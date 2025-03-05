<?php

namespace Models\Domain\Brokers;

use Zephyrus\Database\DatabaseBroker;
use Models\Domain\Entities\Transaction;

class TransactionBroker extends DatabaseBroker
{
    public function save(Transaction $transaction): ?Transaction
    {
        $row = $this->selectSingle("
        INSERT INTO transaction (userId, itemName, price, quantity) 
        VALUES (?, ?, ?, ?) RETURNING id", [
                $transaction->userId,
                $transaction->itemName,
                $transaction->price,
                $transaction->quantity
            ]
        );

        if (!$row || !isset($row['id'])) {
            return null;
        }

        $transaction->id = $row['id'];

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

        return array_map(fn($row) => Transaction::mapToTransaction((object) $row), $rows);
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

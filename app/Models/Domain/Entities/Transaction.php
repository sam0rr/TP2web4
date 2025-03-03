<?php

namespace Models\Domain\Entities;

use Models\Core\Entity;

class Transaction extends Entity
{
    public int $id;
    public int $userId;
    public string $itemName;
    public float $price;
    public int $quantity;
    public float $totalPrice;
    public string $createdAt;

    public static function mapToTransaction(object $row): Transaction
    {
        $transaction = new Transaction();
        $transaction->id = $row->id;
        $transaction->userId = $row->userid;
        $transaction->itemName = $row->itemname;
        $transaction->price = $row->price;
        $transaction->quantity = $row->quantity;
        $transaction->totalPrice = $row->totalprice;
        $transaction->createdAt = $row->createdat;

        return $transaction;
    }
}

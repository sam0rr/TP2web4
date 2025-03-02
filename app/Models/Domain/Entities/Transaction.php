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
}

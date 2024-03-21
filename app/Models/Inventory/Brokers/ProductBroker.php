<?php namespace Models\Organisation\Brokers\Product;

use stdClass;
use Zephyrus\Database\DatabaseBroker;

class ProductBroker extends DatabaseBroker
{
    public function findAll(): array
    {
        return $this->select("SELECT * FROM product");
    }

    public function findById(int $productId): ?stdClass
    {
        return $this->selectSingle("SELECT * FROM product WHERE product_id = ?", [$productId]);
    }

    public function insert(stdClass $product): int
    {
        return $this->selectSingle("INSERT INTO product(provider, brand, name, price) 
                                               VALUES (?, ?, ?, ?) RETURNING product_id", [
            $product->provider,
            $product->brand,
            $product->name,
            $product->price
        ])->product_id;
    }

    public function update(int $productId, stdClass $product): int
    {
        $this->query("UPDATE product 
                               SET provider = ?, brand = ?, name = ?, price = ?
                             WHERE product_id = ?", [
            $product->provider,
            $product->brand,
            $product->name,
            $product->price,
            $productId
        ]);
        return $this->getLastAffectedCount();
    }

    public function delete(int $productId): int
    {
        $this->query("DELETE FROM product WHERE product_id = ?", [$productId]);
        return $this->getLastAffectedCount();
    }

    public function deleteAll(array $productIds): int
    {
        $deleted = 0;
        foreach ($productIds as $productId) {
            $deleted += $this->delete($productId);
        }
        return $deleted;
    }
}

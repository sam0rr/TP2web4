<?php namespace Models\Organisation\Services\Product;

use Models\Organisation\Brokers\Product\ProductBroker;
use Models\Organisation\Validators\Product\ProductValidator;
use stdClass;
use Zephyrus\Application\Form;

class ProductService
{
    public static function readAll(): array
    {
        return (new ProductBroker())->findAll();
    }

    public static function read(int $productId): ?stdClass
    {
        return (new ProductBroker())->findById($productId);
    }

    public static function insert(Form $form): stdClass
    {
        ProductValidator::assert($form);
        $broker = new ProductBroker();
        $productId = $broker->insert($form->buildObject());
        return $broker->findById($productId);
    }

    public static function update(int $productId, Form $form): stdClass
    {
        ProductValidator::assert($form);
        $broker = new ProductBroker();
        $broker->update($productId, $form->buildObject());
        return $broker->findById($productId);
    }

    public static function remove(int $productId): int
    {
        return (new ProductBroker())->delete($productId);
    }

    public static function removeAll(array $productIds): int
    {
        return (new ProductBroker())->deleteAll($productIds);
    }
}

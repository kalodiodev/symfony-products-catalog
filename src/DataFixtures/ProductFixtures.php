<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getProducts() as [$sku, $mpn, $title, $meta_title, $description, $meta_description, $price, $quantity, $enabled]) {
            $product = new Product();
            $product->setSku($sku);
            $product->setMpn($mpn);
            $product->setTitle($title);
            $product->setMetaTitle($meta_title);
            $product->setDescription($description);
            $product->setMetaDescription($meta_description);
            $product->setPrice($price);
            $product->setQuantity($quantity);
            $product->setEnabled($enabled);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getProducts(): array
    {
        return [
            ['A100', 'F-100', 'Product 1', 'product 1', 'Product 1 Description', 'product 1 description', 100, 10, true],
            ['A101', 'F-101', 'Product 2', 'product 2', 'Product 2 Description', 'product 2 description', 120, 9, true],
        ];
    }
}

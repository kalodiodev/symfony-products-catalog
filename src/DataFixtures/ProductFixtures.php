<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getProducts() as [$title, $description, $price]) {
            $product = new Product();
            $product->setTitle($title);
            $product->setDescription($description);
            $product->setPrice($price);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getProducts(): array
    {
        return [
            ['Product 1', 'Product 1 Description', 100],
            ['Product 2', 'Product 2 Description', 120],
        ];
    }
}

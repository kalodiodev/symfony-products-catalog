<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->categories() as [$name, $description, $slug]) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($description);
            $category->setSlug($slug);

            $manager->persist($category);
        }

        $manager->flush();
    }

    public function categories()
    {
        return [
            ['Smartphones', 'All smartphones', 'smartphones'],
            ['Tablets', 'All tablets', 'tablets'],
            ['Laptops', 'All laptops', 'laptops']
        ];
    }
}

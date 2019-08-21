<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->categories() as [$name, $details, $slug]) {
            $brand = new Brand();
            $brand->setName($name);
            $brand->setDetails($details);
            $brand->setSlug($slug);

            $manager->persist($brand);
        }

        $manager->flush();
    }

    public function categories()
    {
        return [
            ['One', 'Creating the best laptops', 'one'],
            ['Two', 'Electronics', 'two'],
            ['Three', 'Tablets and Smartphones', 'three']
        ];
    }
}

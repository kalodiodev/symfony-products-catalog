<?php

namespace App\DataFixtures;

use App\Entity\Attribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AttributeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getAttributes() as [$name, $description]) {
            $attribute = new Attribute();
            $attribute->setName($name);
            $attribute->setDescription($description);

            $manager->persist($attribute);
        }

        $manager->flush();
    }

    protected function getAttributes()
    {
        yield ['Color', 'Color of product'];
        yield ['Material', 'Material of product'];
        yield ['Battery', 'Capacity of battery'];
    }
}

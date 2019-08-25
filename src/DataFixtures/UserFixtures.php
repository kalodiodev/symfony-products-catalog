<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUsers() as [$email, $name, $password]) {
            $user = new User();
            $user->setEmail($email);
            $user->setName($name);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

            $manager->persist($user);
            $manager->flush();
        }
    }

    public function getUsers(): array
    {
        return [
            ['test@example.com', 'John Doe', 'password'],
            ['jane@example.com', 'Jane Smith', 'password'],
        ];
    }
}

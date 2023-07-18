<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $plaintextPassword = 'password';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setUsername("admin")
            ->setEmail("admin@gmail.com")
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_ADMIN']);
            
        $manager->persist($user);
        $manager->flush();
        $this->addReference('user', $user);
    }
}

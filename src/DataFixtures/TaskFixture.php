<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @codeCoverageIgnore
 */
class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0; $i < 10; $i++) { 
            $trick = new Task();

            $trick->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setContent($faker->text)
                ->setUser($this->getReference('user'))
                ->setCreatedAt(new \DateTime());

            $manager->persist($trick);
            $manager->flush();

            $this->addReference('trick'.$i, $trick);
        }
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
        );
    }
}

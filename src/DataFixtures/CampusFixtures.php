<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CampusFixtures extends Fixture
{
    const CAMPUS_REFERENCE = "campus";

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 3; $i++) {
            $campus = new Campus();

            $campus->setNom($faker->word);
            $manager->persist($campus);
            $this->addReference(self::CAMPUS_REFERENCE . $i, $campus);
        }
        $manager->flush();
    }
}

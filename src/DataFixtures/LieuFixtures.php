<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LieuFixtures extends Fixture  implements DependentFixtureInterface
{
    public const LIEU_REFERENCE = "lieu";

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $lieu = new Lieu();

            $lieu->setNom($faker->city())
                ->setRue($faker->streetAddress())
                ->setLatitude($faker->latitude(42.3, 51.1))
                ->setLongitude($faker->longitude(-5.1, 9.6));

            $villeName = VilleFixtures::VILLE_REFERENCE . $faker->numberBetween(0, 3);
            $ville = $this->getReference($villeName, Ville::class);
            $lieu->setVille($ville);

            $manager->persist($lieu);
            $this->addReference(self::LIEU_REFERENCE . $i, $lieu);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VilleFixtures::class,
        ];
    }
}

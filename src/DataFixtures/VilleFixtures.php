<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public const VILLES = [
        [ "nom" => "Rennes",  "cp"  => 35000 ],
        [ "nom" => "Nantes", "cp"  => 44000 ],
        [  "nom" => "Quimper",  "cp"  => 29000 ],
        [ "nom" => "Niort", "cp"  => 79000 ],
    ];

    public const VILLE_REFERENCE = "ville";

    public function load(ObjectManager $manager): void
    {
        foreach (self::VILLES as $key => $villeData) {

            $ville = new Ville();
            $ville->setNom($villeData["nom"])
                ->setCp($villeData["cp"]);

            $manager->persist($ville);
            $this->setReference(self::VILLE_REFERENCE . $key, $ville);
        }

        $manager->flush();
    }
}

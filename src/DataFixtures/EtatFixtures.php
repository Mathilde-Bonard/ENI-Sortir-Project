<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    const ETATS = [
        "CREEE",
        "OUVERTE",
        "CLOTUREE",
        "ACTIVE",
        "EN COURS",
        "PASSEE",
        "ANNULEE"
    ];

    public const ETAT_REFERENCE = 'etat';

    public function load(ObjectManager $manager): void
    {
        foreach (self::ETATS as $key => $etatName) {
            $etat = new Etat();
            $etat->setLibelle($etatName);

            $manager->persist($etat);

            // On stocke une référence pour pouvoir les réutiliser dans WishFixtures
            $this->addReference(self::ETAT_REFERENCE . $key, $etat);
        }
        $manager->flush();
    }
}

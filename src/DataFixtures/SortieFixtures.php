<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public const SORTIE_REFERENCE = 'sortie';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 10; $i++) {
            $sortie = new Sortie();

            $sortie->setNom($faker->word)
                ->setDuree($faker->numberBetween(1, 48))
                ->setDateHeureDebut($faker->dateTimeBetween('+7 days', '+1 months'));

            $sortie->setDateLimiteInscription($faker->dateTime($sortie->getDateHeureDebut()->modify('-12 hours')))
                ->setNbInscriptionMax($faker->numberBetween(20, 100))
                ->setInfosSortie($faker->paragraph());

            $campusName = CampusFixtures::CAMPUS_REFERENCE . $faker->numberBetween(0, 2);
            $campus = $this->getReference($campusName, Campus::class);
            $sortie->setCampus($campus);

            $etatName = EtatFixtures::ETAT_REFERENCE . $faker->numberBetween(0, 6);
            $etat = $this->getReference($etatName, Etat::class);
            $sortie->setEtat($etat);

            $lieuName = LieuFixtures::LIEU_REFERENCE . $faker->numberBetween(0, 9);
            $lieu = $this->getReference($lieuName, Lieu::class);
            $sortie->setLieu($lieu);

            $organisateurName = UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 9);
            $organisateur = $this->getReference($organisateurName, User::class);
            $sortie->setOrganisateur($organisateur);

            $index = $faker->numberBetween(0, 5);

            for ($j = 0; $j < $index; $j++) {
                $participantName = UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 9);
                $participant = $this->getReference($participantName, User::class);

                $sortie->addParticipant($participant);
            }
            $manager->persist($sortie);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            LieuFixtures::class,
            EtatFixtures::class,
        ];
    }
}

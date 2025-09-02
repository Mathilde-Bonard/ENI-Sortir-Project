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

        for($i = 0; $i < 30; $i++) {
            $sortie = new Sortie();

            $sortie->setNom($faker->title)
                ->setDuree($faker->numberBetween(1, 48))
                ->setDateHeureDebut($faker->dateTimeBetween('+7 days', '+1 months'));

            $dateLimite = (clone $sortie->getDateHeureDebut())->modify('-1 day');
            $sortie->setDateLimiteInscription($dateLimite)
                ->setNbInscriptionMax($faker->numberBetween(20, 100))
                ->setInfosSortie($faker->paragraph());

            $campusName = CampusFixtures::CAMPUS_REFERENCE . $faker->numberBetween(0, 2);
            $campus = $this->getReference($campusName, Campus::class);
            $sortie->setCampus($campus);

            $etatName = EtatFixtures::ETAT_REFERENCE . $faker->numberBetween(0, 5);
            $etat = $this->getReference($etatName, Etat::class);
            $sortie->setEtat($etat);

            $lieuName = LieuFixtures::LIEU_REFERENCE . $faker->numberBetween(0, 19);
            $lieu = $this->getReference($lieuName, Lieu::class);
            $sortie->setLieu($lieu);

            $organisateurName = UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 49);
            $organisateur = $this->getReference($organisateurName, User::class);
            $sortie->setOrganisateur($organisateur);

            $participantsAdded = [$organisateurName];

            $index = $faker->numberBetween(0, 40);

            for ($j = 0; $j < $index; $j++) {
                do {
                    $participantName = UserFixtures::USER_REFERENCE . $faker->numberBetween(0, 49);
                } while (in_array($participantName, $participantsAdded)); // On boucle tant qu’on tombe sur un doublon

                $participant = $this->getReference($participantName, User::class);
                $sortie->addParticipant($participant);

                $participantsAdded[] = $participantName; // On ajoute pour éviter un doublon futur
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

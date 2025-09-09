<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class SortieEtatUpdater
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(Sortie $sortie): void
    {
        $now = new \DateTime('now');
        $etat = $sortie->getEtat();
        $finSortie = (clone $sortie->getDateHeureDebut())->modify("+{$sortie->getDuree()} hours");

        switch ($etat) {
            case "CREEE":
            case "ANNULEE":
            case "ARCHIVEE":
                break;

            case "PASSEE":
                if ($now >= (clone $finSortie)->modify("+1 month")) { $etatUpdated = "HISTORISEE"; }
                break;

            default:

                if ($now >= $sortie->getDateHeureDebut() && $now < $finSortie) {
                    $etatUpdated = 'ACTIVITE_EN_COURS';
                } elseif ($now >= $finSortie) {
                    $etatUpdated = 'PASSEE';
                } elseif (count($sortie->getParticipants()) == $sortie->getNbInscriptionMax() || $now > $sortie->getDateHeureDebut()) {
                    $etatUpdated = 'INSC_CLOTUREE';
                } else {
                    $etatUpdated = 'INSC_OUVERTE';
                }

                if ($etat->getLibelle() !== $etatUpdated) {
                    $etatEntity = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => $etatUpdated]);
                    $sortie->setEtat($etatEntity);
                    $this->entityManager->persist($sortie);
                    $this->entityManager->flush();
                }
                break;
        }
    }

    public function updateAll(array $sorties): void
    {
        foreach ($sorties as $sortie) {
            $this->update($sortie);
        }
    }
}
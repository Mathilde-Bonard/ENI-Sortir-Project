<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class SortieCanceler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function cancel(Sortie $sortie): Sortie {
        $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => "ANNULEE"]);

        $sortie->setEtat($etat);
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        return $sortie;
    }
}
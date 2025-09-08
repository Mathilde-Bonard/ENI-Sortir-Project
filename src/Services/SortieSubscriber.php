<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SortieSubscriber
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function subscription(Sortie $sortie, User $user): array
    {
        if ($sortie->getEtat()->getLibelle() !== "INSC_OUVERTE") {
            return [
                'success' => false,
                'subscribed' => null,
                'message' => "Les inscriptions ne sont pas ouvertes."
            ];
        }

        $subscribed = $sortie->toggleParticipant($user);
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        return [
            'success' => true,
            'subscribed' => $subscribed,
            'message' => $subscribed
                ? "Vous êtes inscrit à la sortie."
                : "Vous êtes désinscrit de la sortie."
        ];
    }
}
<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieSubscriber
{
    private EntityManagerInterface $entityManager;
    private Security $security;


    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function sub(Sortie $sortie): bool
    {
        $user = $this->security->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $sortie->addParticipant($user);
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        return true;
    }

    public function unSub(Sortie $sortie): bool
    {
        $user = $this->security->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $sortie->removeParticipant($user);
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();

        return true;
    }
}
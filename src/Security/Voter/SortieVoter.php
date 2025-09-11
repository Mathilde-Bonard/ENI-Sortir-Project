<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SortieVoter extends Voter
{
    public const PUBLISH = "SORTIE_PUBLISH";
    public const CANCEL = 'SORTIE_CANCEL';
    public const EDIT = 'SORTIE_EDIT';
    public const READ_PARTICIPANTS = 'SORTIE_READ_PARTICIPANTS';
    public const UNSUBSCRIBE = 'SORTIE_UNSUBSCRIBE';
    public const REMOVE_PARTICIPANT = 'SORTIE_REMOVE_PARTICIPANT';
    public const SUBSCRIBE = 'SORTIE_SUBSCRIBE';

    public function __construct(private Security $security) // injection de Security
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [
                self::CANCEL,
                self::EDIT,
                self::READ_PARTICIPANTS,
                self::UNSUBSCRIBE,
                self::SUBSCRIBE,
                self::REMOVE_PARTICIPANT,
                self::PUBLISH,
            ])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $etat = $subject->getEtat()->getLibelle();
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::PUBLISH:
                return $user === $subject->getOrganisateur()
                    && $subject->getDateLimiteInscription() > new \DateTime();

            case self::CANCEL:
                return $user === $subject->getOrganisateur()
                    || $this->security->isGranted('ROLE_ADMIN')
                    && $etat == "INSC_OUVERTE";

            case self::EDIT:
            case self::REMOVE_PARTICIPANT:
                return $user === $subject->getOrganisateur()
                    && $etat !== 'ANNULEE'
                    && $etat !== 'PASSEE';

            case self::UNSUBSCRIBE:
                return $this->security->isGranted('ROLE_USER')
                    && $subject->getParticipants()->contains($user)
                    && $etat !== 'ACTIVITE_EN_COURS'
                    && $etat !== 'ANNULEE'
                    && $etat !== 'PASSEE';


            case self::READ_PARTICIPANTS:
                return $this->security->isGranted('ROLE_USER')
                    && $subject->getParticipants()->contains($user)
                    || $user === $subject->getOrganisateur()
                    || $etat === 'PASSEE';

            case self::SUBSCRIBE:
                return $this->security->isGranted('ROLE_USER')
                    && count($subject->getParticipants()) < $subject->getNbInscriptionMax()
                    && !$subject->getParticipants()->contains($user)
                    && $subject->getDateLimiteInscription() > new \DateTime();

            default:
                return false;
        }

        return false;
    }
}

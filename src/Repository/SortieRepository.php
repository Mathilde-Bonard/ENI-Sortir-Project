<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function readAllDateDesc()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.participants', 'p')
            ->leftJoin('s.etat', 'e')
            ->addSelect('o')
            ->addSelect('p')
            ->addSelect('e')
            ->addOrderBy('s.dateHeureDebut', 'ASC')
            ->andWhere("e.libelle != 'PASSEE'");

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function readByFilter(mixed $data)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.participants', 'p')
            ->leftJoin('s.etat', 'e')
            ->addSelect('o')
            ->addSelect('p')
            ->addSelect('e')
            ->andWhere('e.libelle != :passee')
            ->setParameter('passee', 'PASSEE')
            ->orderBy('s.dateHeureDebut', 'ASC');

        if (!empty($data['campus'])) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $data['campus']);
        }
        if (!empty($data['nom'])) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $data['nom'] . '%');
        }

        $dateDebut = $data['dateIntervalDebut'] ?? null;
        $dateFin   = $data['dateIntervalFin'] ?? null;

        if ($dateDebut || $dateFin) {
            if ($dateDebut && $dateFin) {
                $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $dateDebut)
                    ->setParameter('dateFin', $dateFin);
            } elseif ($dateDebut) {
                $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                    ->setParameter('dateDebut', $dateDebut);
            } else {
                $qb->andWhere('s.dateHeureDebut <= :dateFin')
                    ->setParameter('dateFin', $dateFin);
            }
        }
        return $qb->getQuery()->getResult();
    }
}

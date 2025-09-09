<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\SortieFilter;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function readById(int $id): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')->addSelect('e')
            ->leftJoin('s.participants', 'p')->addSelect('p')
            ->leftJoin('s.lieu', 'l')->addSelect('l')
            ->leftJoin('l.ville', 'v')->addSelect('v') // si tu veux la ville
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function readAll(SortieFilter $data = null, User $user = null)
    {
        $qb = $this->createQueryBuilder('s')->leftJoin('s.organisateur', 'o')->addSelect('o')
            ->leftJoin('s.participants', 'p')->addSelect('p')
            ->leftJoin('s.etat', 'e')->addSelect('e')
            ->orderBy('s.dateHeureDebut', 'ASC');

        $hasPasseFilter = isset($data) && in_array('PASSEE', $data->getFilters());

        if (!$hasPasseFilter) {
            $qb->andWhere("e.libelle NOT IN ('CREEE', 'PASSEE')");
        }

        if($data) {
            $this->addFilter($qb, $data, $user);
        }
        return $qb->getQuery()->getResult();

    }

    private function addFilter(QueryBuilder $qb, SortieFilter $data, User $user): void
    {
        if ($data->getCampus()) {
            $qb->andWhere('s.campus = :campus')->setParameter('campus', $data->getCampus());
        }
        if ($data->getNom()) {
            $qb->andWhere('s.nom LIKE :nom')->setParameter('nom', '%' . $data->getNom() . '%');
        }

        $dateDebut = $data->getDateIntervalDebut() ?? null;
        $dateFin   = $data->getDateIntervalFin() ?? null;

        if ($dateDebut || $dateFin) {
            if ($dateDebut && $dateFin) {
                $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')->setParameter('dateDebut', $dateDebut)->setParameter('dateFin', $dateFin);
            } elseif ($dateDebut) {
                $qb->andWhere('s.dateHeureDebut >= :dateDebut')->setParameter('dateDebut', $dateDebut);
            } else {
                $qb->andWhere('s.dateHeureDebut <= :dateFin')->setParameter('dateFin', $dateFin);
            }
        }

        $filterMap = [
            'PASSEE' => fn($qb) => $qb->andWhere('e.libelle = :passee')->setParameter('passee', 'PASSEE'),
            'ORGA' => fn($qb) => $user && $qb->andWhere('o = :user')->setParameter('user', $user),
            'INSC' => fn($qb) => $user && $qb->andWhere(':user MEMBER OF s.participants')->setParameter('user', $user),
            'NOT_INSC' => fn($qb) => $user && $qb->andWhere(':user NOT MEMBER OF s.participants')->setParameter('user', $user),
        ];

        foreach ($data->getFilters() ?? [] as $filter) {
            if (isset($filterMap[$filter])) {
                $filterMap[$filter]($qb);
            }
        }
    }
}

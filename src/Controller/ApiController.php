<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiController extends AbstractController
{
    #[Route(path: '/api/lieux/{ville_id}', name: 'lieux_par_ville', methods: ['GET'])]
    public function getLieuxParVille(
        int $ville_id,
        LieuRepository $repository,
        SerializerInterface $serializer,
        ): Response
    {
        $lieux = $repository->createQueryBuilder('l')
            ->where('l.ville = :ville_id')
            ->setParameter('ville_id', $ville_id)
            ->getQuery()
            ->getResult();

        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => ['lieux_par_ville']]);
    }
}

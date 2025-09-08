<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        ): JsonResponse
    {
        $lieux = $repository->findBy(['ville' => $ville_id]);
        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => ['lieux_par_ville']]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
final class ApiController extends AbstractController
{
    #[Route(path:'/ville/{villeId}', name: 'ville_id', methods: ['GET'])]
    public function getVilleById(
        int $villeId,
        VilleRepository $villeRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $ville = $villeRepository->find($villeId);
        if (!$ville) {
            throw $this->createNotFoundException('Ville non trouvée');
        }
        return $this->json($serializer->serialize($ville, 'json'));

    }

    #[Route(path: '/lieux/{ville_id}', name: 'lieux_par_ville', methods: 'GET')]
    public function getLieuxParVille(
        int $ville_id,
        LieuRepository $repository,
        SerializerInterface $serializer,
        ): JsonResponse
    {
        $lieux = $repository->findBy(['ville' => $ville_id]);
        return $this->json($lieux, Response::HTTP_OK, [], ['groups' => ['lieux_par_ville']]);

    }

    #[Route(path: '/lieu/{lieu_id}/detail', name: 'lieu_rue_cp', methods: 'GET')]
    public function getLieuRueCp(
        int $lieu_id,
        LieuRepository $repository,
        ): JsonResponse
    {
        $lieu = $repository->find($lieu_id);
        if (!$lieu) {
            return $this->json(['error' => 'Lieu non trouvé'], 404);
        }

        return $this->json([
            'rue' => $lieu->getRue(),
            'codePostal' => $lieu->getVille()->getCp()
        ]);
    }

    #[Route(path: '/lieu/create', name: 'create', methods: 'POST')]
    public function add(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        VilleRepository $villeRepository,
        ValidatorInterface $validator,
    ): Response
    {
        $data = json_decode($request->getContent());

        $ville = $villeRepository->find($data->ville);

        if (!$ville) {
            return $this->json(['error' => 'Ville non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $lieu = new Lieu();
        $lieu->setVille($ville);
        $lieu->setNom($data->nom);
        $lieu->setRue($data->rue);

        $errors = $validator->validate($lieu);
        if (count($errors) > 0) {
            return $this->json($serializer->serialize($errors, 'json'));
        }
        $entityManager->persist($lieu);
        $entityManager->flush();

        return $this->json($lieu, Response::HTTP_CREATED, [], ['groups' => ['lieu_par_ville']]);

    }


}

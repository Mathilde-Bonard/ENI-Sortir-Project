<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie')]
    #[Route('/accueil', name: 'app_sortie_2')]
    public function index(SortieRepository $sortieRepository): Response
    {

        $sorties = $sortieRepository->readAllDateDesc();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}

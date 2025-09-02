<?php

namespace App\Controller;

use App\Form\FilterType;
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

        $filterForm = $this->createForm(FilterType::class);

        return $this->render('sortie/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $sorties,
        ]);
    }

}

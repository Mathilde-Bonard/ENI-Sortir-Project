<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Entity\Sortie;
use App\Form\SortieCreationType;
=======
use App\Form\FilterType;
use App\Repository\SortieRepository;
>>>>>>> origin/Max
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
<<<<<<< HEAD
    #[Route('/add', name: 'sortie_add')]
    public function add(
        Request $request
    ): Response
    {
        $sortie = new Sortie();
        $sortieFormCreation = $this->createForm(SortieCreationType::class, $sortie);

        $sortieFormCreation->handleRequest($request);

        return $this->render('sortie/create.html.twig', [
            'sortieFormCreation' => $sortieFormCreation
=======
    #[Route('/', name: 'app_sortie')]
    #[Route('/accueil', name: 'app_sortie_2')]
    public function index(SortieRepository $sortieRepository): Response
    {

        $sorties = $sortieRepository->readAllDateDesc();

        $filterForm = $this->createForm(FilterType::class);

        return $this->render('sortie/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $sorties,
>>>>>>> origin/Max
        ]);
    }

}

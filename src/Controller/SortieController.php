<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FilterType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'sortie_')]
final class SortieController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[Route('/accueil', name: 'list_2', methods: ['GET'])]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {

        $sorties = $sortieRepository->readAllDateDesc();

        $filterForm = $this->createForm(FilterType::class, null, [
            'method' => 'GET',
            'action' => $this->generateUrl('sortie_list_2'),
            'csrf_protection' => false,
        ]);

        $filterForm->handleRequest($request);
        if ($request->isXmlHttpRequest() || $request->headers->get('Turbo-Frame')) {
            $sorties = $sortieRepository->readByFilter($filterForm->getData());

            return $this->render('views/sortie/_list.html.twig', [
                'sorties' => $sorties,
            ]);
        }

        return $this->render('sortie/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $sorties,
        ]);
    }


    #[Route('/sortie/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(Sortie $sortie): Response
    {
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }
}

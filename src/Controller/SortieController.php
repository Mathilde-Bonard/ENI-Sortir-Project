<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieCreationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
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
        ]);
    }
}

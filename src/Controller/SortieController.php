<?php

namespace App\Controller;

;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\FilterType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;

use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/add', name: 'sortie_add')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        EtatRepository $etatRepository,
        LieuRepository $lieuRepository,
    ): Response
    {
        $sortie = new Sortie();
        $user = $this->getUser();
        $etatCreee = $etatRepository->findOneBy(['libelle' => 'CREEE']);

        // Pré-selection du campus -> campus du user connecté
        $sortie->setCampus($user->getCampus());

        $sortieFormCreation = $this->createForm(SortieType::class, $sortie);
        $sortieFormCreation->handleRequest($request);

        if ($sortieFormCreation->isSubmitted() && $sortieFormCreation->isValid()) {
            // Non rempli par le user -> organisateur = user, état = CREEE
            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat($etatCreee);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success','Sortie ajoutée !');
            return $this->redirectToRoute('sortie_list');
        }
        // Formulaire d'ajout de lieu dans la modale
        $lieu = new Lieu();
        $lieuFormCreation = $this->createForm(LieuType::class, $lieu);

        $lieuFormCreation->handleRequest($request);
        if ($lieuFormCreation->isSubmitted() && $lieuFormCreation->isValid()) {
            $em->persist($lieu);
            $em->flush();

            $this->addFlash('success', 'Nouveau lieu ajouté !');
        }

    //TODO render lieu.rue et lieu.cp pour la vue

        return $this->render('sortie/create.html.twig', [
            'sortieFormCreation' => $sortieFormCreation->createView(),
            'lieuFormCreation' => $lieuFormCreation->createView(),
        ]);
    }

    #[Route('/sortie/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(Sortie $sortie): Response
    {
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/sortie/modification/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        SortieRepository $sortieRepository,
        Request $request,
        EntityManagerInterface $em,
    ): Response
    {
        $sortie = $sortieRepository->find($id);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if (!$sortie) {
            throw $this->createNotFoundException("Oooops ! l'événement n'a pas été trouvé !");
        }
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success','Sortie modifiée !');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/update.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            ]);
        }
}




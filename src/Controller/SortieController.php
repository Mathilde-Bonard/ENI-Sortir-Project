<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\SortieFilter;
use App\Entity\User;
use App\Form\CancelType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\FilterType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;

use App\Services\SortieCanceler;
use App\Services\SortieEtatUpdater;
use App\Services\SortieSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', name: 'sortie_')]
final class SortieController extends AbstractController
{

    #[IsGranted("ROLE_USER")]
    #[Route('', name: 'list', methods: ['GET'])]
    #[Route('/accueil', name: 'list_2', methods: ['GET'])]
    public function index(Request           $request,
                          SortieRepository  $sortieRepository,
                          SortieEtatUpdater $etatUpdater): Response
    {
        $sorties = $sortieRepository->readAll();
        $etatUpdater->updateAll($sorties);

        $sortieFilter = new SortieFilter();

        $filterForm = $this->createForm(FilterType::class, $sortieFilter, [
            'method' => 'GET',
            'action' => $this->generateUrl('sortie_list_2'),
            'csrf_protection' => false,
        ]);

        $filterForm->handleRequest($request);
        if ($request->isXmlHttpRequest() || $request->headers->get('Turbo-Frame')) {
            $user = $this->getUser();

            if ($user instanceof User) {
                $sorties = $sortieRepository->readAll($filterForm->getData(), $user);
            }

            return $this->render('views/sortie/_list.html.twig', [
                'sorties' => $sorties,
            ]);
        }
        return $this->render('sortie/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/cancel', name: 'cancel', methods: ['GET'])]
    public function cancelRequest(SortieRepository $sortieRepository,
                                  Request          $request): Response
    {
        $id = $request->query->get('sortie');

        $sortie = $sortieRepository->readById($id);

        $cancelForm = $this->createForm(CancelType::class, $sortie, [
            'action' => $this->generateUrl('sortie_cancel_process', ['id' => $sortie->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('views/sortie/_cancel-form.html.twig', [
            "cancelForm" => $cancelForm
        ]);
    }

    #[IsGranted('SORTIE_CANCEL', subject: 'sortie')]
    #[Route('/sortie/cancel/{id}', name: 'cancel_process', methods: ['POST'])]
    public function cancelProcess(
        Request        $request,
        Sortie         $sortie,
        SortieCanceler $canceler
    ): Response
    {
        $form = $this->createForm(CancelType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $canceler->cancel($sortie);

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        // Si pas valide, on réaffiche le formulaire
        return $this->render('views/sortie/_cancel-form.html.twig', [
            'cancelForm' => $form->createView()
        ]);
    }


    #[IsGranted("ROLE_USER")]
    #[Route('/add', name: 'sortie_add')]
    public function add(
        Request                $request,
        EntityManagerInterface $em,
        EtatRepository         $etatRepository,
        LieuRepository         $lieuRepository,
    ): Response
    {

        $user = $this->getUser();

        $etatCreee = $etatRepository->findOneBy(['libelle' => 'CREEE']);

        $sortie = new Sortie();
        $sortie->setCampus($user->getCampus());

        // Pré-selection du campus -> campus du user connecté
        $sortie->setCampus($user->getCampus());

        $sortieFormCreation = $this->createForm(SortieType::class, $sortie);
        $sortieFormCreation->handleRequest($request);

        if ($sortieFormCreation->isSubmitted() && $sortieFormCreation->isValid()) {
            // Non rempli par le user -> organisateur = user, état = CREEE
            $sortie->setOrganisateur($user);
            $sortie->setEtat($etatCreee);

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Sortie ajoutée !');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
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

    #[IsGranted("ROLE_USER")]
    #[Route('/sortie/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(SortieRepository $sortieRepository, int $id, SortieEtatUpdater $etatUpdater): Response
    {
        try {
            $sortie = $sortieRepository->readById($id);

            $etatUpdater->update($sortie);
        } catch (\Exception $e) {
            throw $this->createNotFoundException("Oooops ! l'événement n'a pas été trouvé !");
        }

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[IsGranted("SORTIE_EDIT", subject: "sortie")]
    #[Route('/sortie/modification/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(
        Sortie                 $sortie,
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Sortie modifiée !');
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/update.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }


    #[IsGranted("SORTIE_SUBSCRIBE", subject: "sortie")]
    #[Route('/sortie/{id}/sub', name: 'sub', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function sub(Sortie           $sortie,
                        Request          $request,
                        SortieSubscriber $sortieSubscriber): Response
    {
        $sortieSubscriber->sub($sortie);

        $referer = $request->headers->get('referer');
        $template = str_contains($referer, "/sortie") ? 'views/sortie/_details.html.twig' : 'views/sortie/_sortie.html.twig';

        return $this->render($template, [
            'sortie' => $sortie,
        ]);
    }

    #[IsGranted("SORTIE_UNSUBSCRIBE", subject: "sortie")]
    #[Route('/sortie/{id}/unsub', name: 'unsub', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function unSub(Sortie           $sortie,
                          Request          $request,
                          SortieSubscriber $sortieSubscriber): Response
    {
        $sortieSubscriber->unSub($sortie);

        $referer = $request->headers->get('referer');
        $template = str_contains($referer, "/sortie") ? 'views/sortie/_details.html.twig' : 'views/sortie/_sortie.html.twig';

        return $this->render($template, [
            'sortie' => $sortie,
        ]);
    }


    #[IsGranted("SORTIE_PUBLISH", subject: "sortie")]
    #[Route('/sortie/{id}/publish', name: 'publish', requirements: ['id' => '\d+'])]
    public function publish(Sortie                 $sortie,
                            EtatRepository         $etatRepository,
                            EntityManagerInterface $entityManager): Response
    {
        $etat = $etatRepository->findOneBy(['libelle' => 'INSC_OUVERTE']);

        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }
}




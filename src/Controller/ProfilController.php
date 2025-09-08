<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// ============================ Route principal des fonctions du profil ============================

#[Route('/profil', name: 'app_profil_')]
final class ProfilController extends AbstractController
{

// ================================== Route affichage d'un profil ==================================

    #[Route('/details', name: 'details')]
    public function index(): Response
    {
        return $this->render('profil/details.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    //Route vers la page des détails du profil de l'utilisateur connecté
    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function myProfil(int $id, UserRepository $utilisateurRepository): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Ooooops! Utilisateur introuvable :/');
        }

        return $this->render('profil/details.html.twig', [
            'user' => $utilisateur,
        ]);
    }

// ================================== Route modification d'un profil ==================================

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(
        int $id,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserType::class, $user, [
            'submit_label' => 'Modifier'
        ]);
        $userForm->handleRequest($request);

        if (!$user) {
            throw $this->createNotFoundException('Oooops ! Utilisateur inexistant.');
        }
        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour!');
            return $this->redirectToRoute('app_profil_details', ['id' => $user->getId()]);
        }

        return $this->render('profil/update.html.twig', [
            'userUpdateForm' => $userForm->createView(),
        ]);
    }
}

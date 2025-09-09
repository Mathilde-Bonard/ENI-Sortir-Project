<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// ============================ Route principal des fonctions du profil ============================

#[Route('/profil', name: 'app_profil_')]
final class ProfilController extends AbstractController
{

// ================================== Route affichage d'un profil ==================================

    //Route vers la page des détails du profil d'un utilisateur selon l'id donné en paramètre
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

    #[Route('/update', name: 'update')]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('%photo_dir%')] string $photoDir
    ): Response
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user, [
            'submit_label' => 'Enregistrer'
        ]);
        $userForm->handleRequest($request);
        if (!$user) {
            throw $this->createNotFoundException('Oooops ! Utilisateur inexistant.');
        }
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            if ($photo = $userForm['photo']->getData()) {
                $fileName = uniqid() . '.' . $photo->guessExtension();
                $photo->move($photoDir, $fileName);
                $user->setImageFileName($fileName);
            }
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

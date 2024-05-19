<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route('/gestion/user')]

class GestionUserController extends AbstractController
{
    #[Route('/', name: 'app_gestion_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('gestion_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestion_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestion_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('gestion_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestion_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestion_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestion_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_gestion_user_search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository): Response
    {
        $email = $request->query->get('email');

        // Vérifiez si l'e-mail est défini
        if ($email) {
            // Utilisez la méthode du repository pour rechercher l'utilisateur par e-mail
            $user = $userRepository->findOneByEmail($email);

            // Vérifiez si un utilisateur a été trouvé
            if ($user) {
                // Si un utilisateur est trouvé, redirigez vers la page de détails de l'utilisateur
                return $this->redirectToRoute('app_gestion_user_show', ['id' => $user->getId()]);
            } else {
                // Si aucun utilisateur n'est trouvé, affichez un message d'erreur
                $this->addFlash('warning', 'User not found with email: '.$email);
            }
        }

        // Redirigez toujours vers la page de liste des utilisateurs si aucun e-mail n'est spécifié ou si aucun utilisateur n'est trouvé
        return $this->redirectToRoute('app_gestion_user_index');
    }
}


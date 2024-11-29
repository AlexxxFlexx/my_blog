<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'user_list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $userRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list');
    }

    #[Route('/admin/users/promote/{id}', name: 'user_promote', methods: ['POST'])]
    public function promote(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $userRepository->find($id);
        if ($this->isCsrfTokenValid('promote' . $user->getId(), $request->request->get('_token'))) {
            $user->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list');
    }
}

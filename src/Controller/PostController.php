<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post')]
    public function list(Request $request, PostRepository $postRepository): Response
    {
        $sortBy = $request->query->get('sort', 'newest');

        switch ($sortBy) {
            case 'oldest':
                $posts = $postRepository->findBy([], ['publishedAt' => 'ASC']);
                break;
            case 'category':
                $posts = $postRepository->findBy([], ['category' => 'ASC', 'publishedAt' => 'DESC']);
                break;
            case 'newest':
            default:
                $posts = $postRepository->findBy([], ['publishedAt' => 'DESC']);
                break;
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'currentSort' => $sortBy
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setPublishedAt(new \DateTime());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post');
        }

        return $this->render('post/new.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    #[Route('/post/edit/{id}', name: 'post_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('post');
        }

        return $this->render('post/edit.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    #[Route('/post/delete/{id}', name: 'post_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('post');
    }

    #[Route('/post/{id}', name: 'post_show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}

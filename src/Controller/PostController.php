<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostRepository;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post_list')]
    public function list(PostRepository $postRepository): Response
    {
        $posts = $postRepository->getAllPost();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}

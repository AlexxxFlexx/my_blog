<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewPostController extends AbstractController
{
    #[Route('/new/post', name: 'post_new')]
    public function index(): Response
    {
        return $this->render('post/new.html.twig', [
            'controller_name' => 'NewPostController',
        ]);
    }
}

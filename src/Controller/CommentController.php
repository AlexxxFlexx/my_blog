<?php
// src/Controller/CommentController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/post/{id}/comment', name: 'comment_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setContent($request->request->get('content'));
        $comment->setCreatedAt(new \DateTime());
        $comment->setUser($this->getUser());
        $comment->setPost($post);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            if ($this->isGranted('ROLE_ADMIN') || $comment->getUser() === $this->getUser()) {
                $postId = $comment->getPost()->getId();
                $entityManager->remove($comment);
                $entityManager->flush();
                return $this->redirectToRoute('post_show', ['id' => $postId]);
            }
        }
        return $this->redirectToRoute('post_show', ['id' => $comment->getPost()->getId()]);
    }
}

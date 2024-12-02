<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route(name: 'app_comment_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $comments = $entityManager
            ->getRepository(Comment::class)
            ->findAll();

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

        #[Route('/new', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the post ID from the request
        $postId = $request->request->get('post_id');
        $content = $request->request->get('content');
        $csrfToken = $request->request->get('_token');

        // Validate CSRF token
        if (!$this->isCsrfTokenValid('new_comment', $csrfToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        // Find the post
        $post = $entityManager->getRepository(Post::class)->find($postId);
        if (!$post) {
            throw $this->createNotFoundException('Post not found.');
        }

        // Create and save the new comment
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setPost($post);

        $entityManager->persist($comment);
        $entityManager->flush();

        // Redirect back to the post's show page
        return $this->redirectToRoute('app_post_show', [
            'forum_id' => $post->getForum()->getId(),
            'id' => $post->getId(),
        ]);
    }


    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $content = $request->request->get('content');
        $csrfToken = $request->request->get('_token');
    
        if ($this->isCsrfTokenValid('edit' . $comment->getId(), $csrfToken) && $content) {
            $comment->setContent($content);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_post_show', [
                'forum_id' => $comment->getPost()->getForum()->getId(), // Pass forum_id
                'id' => $comment->getPost()->getId(),                  // Pass post_id
            ]);
        }
    
        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
    

        #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $csrfToken)) {
            $entityManager->remove($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', [
                'forum_id' => $comment->getPost()->getForum()->getId(), // Pass forum_id
                'id' => $comment->getPost()->getId(),                  // Pass post_id
            ]);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

}

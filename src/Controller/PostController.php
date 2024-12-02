<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum/{forum_id}/posts')]
final class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(int $forum_id, EntityManagerInterface $entityManager): Response
    {
        // Find the forum by the forum_id
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);

        if (!$forum) {
            throw $this->createNotFoundException('Forum not found.');
        }

        // Find all posts for this forum
        $posts = $entityManager
            ->getRepository(Post::class)
            ->findBy(['forum' => $forum]);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'forum' => $forum,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $forum_id, EntityManagerInterface $entityManager): Response
    {
        // Find the forum by the forum_id
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);

        if (!$forum) {
            throw $this->createNotFoundException('Forum not found.');
        }

        $post = new Post();
        $post->setForum($forum);  // Associate the new post with the forum

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', ['forum_id' => $forum->getId()]);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'forum' => $forum,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(int $forum_id, Post $post, EntityManagerInterface $entityManager): Response
    {
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);
        if (!$forum || $post->getForum()->getId() !== $forum_id) {
            throw $this->createNotFoundException('Forum or Post not found.');
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'forum' => $forum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $forum_id, Post $post, EntityManagerInterface $entityManager): Response
    {
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);
        if (!$forum || $post->getForum()->getId() !== $forum_id) {
            throw $this->createNotFoundException('Forum or Post not found.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', ['forum_id' => $forum->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'forum' => $forum,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, int $forum_id, Post $post, EntityManagerInterface $entityManager): Response
    {
        $forum = $entityManager->getRepository(Forum::class)->find($forum_id);
        if (!$forum || $post->getForum()->getId() !== $forum_id) {
            throw $this->createNotFoundException('Forum or Post not found.');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', ['forum_id' => $forum->getId()]);
    }
}

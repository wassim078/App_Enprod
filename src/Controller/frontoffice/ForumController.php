<?php

namespace App\Controller\frontoffice;

use App\Entity\CategorieForum;
use App\Entity\Post;
use App\Entity\PostRating;
use App\Form\PostType;
use App\Form\RatingType;
use App\Repository\CategorieForumRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/templatefrontoffice')]
class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(CategorieForumRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('templatefrontoffice/categorie_forum/index.html.twig', [
            'categories' => $categories,
        ]);
    }



    #[Route('/forum/{id}', name: 'app_forum_posts')]
    public function showPosts(CategorieForum $categorie, PostRepository $postRepository): Response
    {


        $posts = $postRepository->findByCategory($categorie);
        $userRatings = [];
        if ($this->getUser()) {
            foreach ($posts as $post) {
                foreach ($post->getRatings() as $rating) {
                    if ($rating->getUser() === $this->getUser()) {
                        $userRatings[$post->getId()] = $rating->getRating();
                    }
                }
            }
        }

        return $this->render('templatefrontoffice/categorie_forum/posts.html.twig', [
            'categorie' => $categorie,
            'posts' => $posts,
            'user_ratings' => $userRatings
        ]);
    }


    #[Route('/forum/{id}/new-post', name: 'app_forum_new_post', methods: ['GET', 'POST'])]
    public function newPost(
        CategorieForum $categorie,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Only allow authenticated users
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set relations automatically
            $post->setCategorie($categorie);
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post created successfully!');
            return $this->redirectToRoute('app_forum_posts', ['id' => $categorie->getId()]);
        }

        return $this->render('templatefrontoffice/categorie_forum/new_post.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }


    #[Route('/forum/{id}/rate/{post}', name: 'rate_post', methods: ['POST'])]
    public function ratePost(
        CategorieForum $categorie,
        Post $post,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $rating = $post->getRatings()->filter(
            fn(PostRating $r) => $r->getUser() === $this->getUser()
        )->first() ?: new PostRating();

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setPost($post);
            $rating->setUser($this->getUser());

            $entityManager->persist($rating);
            $post->addRating($rating);
            $entityManager->flush(); // Only flush once

            $this->addFlash('success', 'Rating submitted!');
        }

        return $this->redirectToRoute('app_forum_posts', ['id' => $categorie->getId()]);
    }

}
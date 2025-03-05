<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\CategorieForum;
use App\Repository\CategorieForumRepository;

use App\Form\Post1Type;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/templatefrontoffice/post')]
final class PostController extends AbstractController{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('templatefrontoffice/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer un Forum en fonction de l'ID
        $forum = $entityManager->getRepository(CategorieForum::class)->find($id);
    

        // Créer un nouveau Post
        $post = new Post();
        
        // Associer le Post au Forum récupéré
        $post->setForum($forum);
    
        // Créer et gérer le formulaire pour le Post
        $form = $this->createForm(Post1Type::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();
    
            // Rediriger après la création du Post
            return $this->redirectToRoute('app_forum_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('templatefrontoffice/post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'forum' => $forum, 
            'id'=>$id,// Passer le Forum au template
        ]);
    }
    

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('templatefrontoffice/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Post1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('templatefrontoffice/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rate/post', name: 'app_rate_post', methods: ['POST'])]
    public function ratePost(Request $request)
    {
        // Récupérer les données envoyées
        $data = json_decode($request->getContent(), true);
        $postId = $data['postId'];  // Identifiant du post
        $rating = (int) $data['rating'];  // Note donnée par l'utilisateur
        $userId = $data['userId'];  // Identifiant de l'utilisateur
        $date = (new \DateTime())->format('Y-m-d\TH:i:s');  // Date actuelle au format ISO 8601
    
        // Chemin vers le fichier JSON des évaluations
        $filePath = '../public/rating.json';
    
        // Lire les données depuis le fichier JSON
        $ratings = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    
        // Si le post n'existe pas encore dans les évaluations, on l'initialise
        if (!isset($ratings[$postId])) {
            $ratings[$postId] = ['average' => 0];
        }
    
        // Vérifier si l'utilisateur a déjà noté ce post
        if (isset($ratings[$postId][$userId])) {
            // L'utilisateur a déjà noté → Mettre à jour la note
            $ratings[$postId][$userId]['rating'] = $rating;
            $ratings[$postId][$userId]['date'] = $date;
        } else {
            // Nouvel enregistrement
            $ratings[$postId][$userId] = [
                'rating' => $rating,
                'date' => $date,
                'userId' => $userId
            ];
        }
    
        // Recalculer la moyenne
        $sum = 0;
        $count = 0;
        foreach ($ratings[$postId] as $key => $evaluation) {
            if ($key !== 'average' && isset($evaluation['rating'])) {  
                // On ignore la clé "average" qui n'est pas une évaluation
                $sum += $evaluation['rating'];
                $count++;
            }
        }
        $average = $count > 0 ? $sum / $count : 0;
    
        // Mettre à jour la moyenne dans le fichier JSON
        $ratings[$postId]['average'] = round($average, 2);
    
        // Sauvegarder les données dans le fichier JSON
        if (file_put_contents($filePath, json_encode($ratings, JSON_PRETTY_PRINT)) === false) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la sauvegarde des données.'], 500);
        }
    
        // Retourner la réponse avec la moyenne et le nombre total d'évaluations
        return new JsonResponse([
            'success' => true,
            'average' => round($average, 2),
            'moyenne' => $average
        ]);
    }


    #[Route('/post/search/{id}', name: 'app_post_search', methods: ['GET'])]
    public function search(int $id, Request $request, PostRepository $postRepository, CategorieForumRepository $categorieForumRepository): Response
    {
        // Récupérer la catégorie du forum
        $categorieForum = $categorieForumRepository->find($id);
    
        // Lire le fichier JSON des moyennes
        $filePath = '../public/rating.json';
        $ratings = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    
        // Récupérer tous les posts de la catégorie avec tri par date
        $posts = $postRepository->findBy(['forum' => $categorieForum], ['createdAt' => 'DESC']);
    
        // Associer les moyennes aux posts
        $postRatings = [];
        foreach ($posts as $post) {
            $postId = $post->getId();
            $postRatings[$postId] = $ratings[$postId]['average'] ?? null;
        }
    
        // Recherche par titre
        $query = $request->query->get('q', '');
        $posts = $postRepository->createQueryBuilder('p')
            ->where('p.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    
        // Vérifier si la requête est une requête AJAX
        if ($request->isXmlHttpRequest()) {
            // Renvoyer uniquement la partie HTML des résultats de recherche
            return $this->render('templatefrontoffice/post/_post_list.html.twig', [
                'posts' => $posts,
                'moyenne' => $postRatings, // Ajouter les moyennes au template
            ]);
        }
    
        // Sinon, on renvoie la page complète
        return $this->render('templatefrontoffice/forum_show.html.twig', [
            'posts' => $posts,
            'categorie_forum' => $categorieForum,
            'id' => $id,
            'moyenne' => $postRatings, // Ajouter les moyennes au template
        ]);
    }
    
}
 
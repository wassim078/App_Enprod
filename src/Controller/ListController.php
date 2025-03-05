<?php
namespace App\Controller;

use App\Repository\CategorieForumRepository;
use App\Repository\PostRepository;
use App\Repository\CommentaireRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Forum;
use App\Entity\Post;
use App\Entity\Commentaire;

use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class ListController extends AbstractController
{
    #[Route('/templatefrontoffice/forum', name: 'app_list')]
    public function index(CategorieForumRepository $categorieForumRepository): Response
    {
        $categorieForums = $categorieForumRepository->findAll();

        return $this->render('templatefrontoffice/forum.html.twig', [
            'categorie_forums' => $categorieForums,
        ]);
    }
    #[Route('/post/{id}/comment', name: 'app_comment_create', methods: ['POST'])]
    public function createComment(Request $request, PostRepository $postRepository, EntityManagerInterface $em, $id): Response
    {
        // Récupérer le post
        $post = $postRepository->find($id);
    
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
    
        // Créer un nouveau commentaire
        $comment = new Commentaire();
        $comment->setContent($request->request->get('content'));
        $comment->setPost($post);
        $comment->setUser($this->getUser()); // Utilisateur connecté
        $comment->setCreatedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());

    
        // Enregistrer le commentaire
        $em->persist($comment);
        $em->flush();
    
        // Rediriger ou retourner une réponse
        $this->addFlash('success', 'Commentaire ajouté avec succès.');
        return $this->redirectToRoute('app_forum_show', ['id' => $post->getcategorieForum()->getId()]);
    }
    #[Route('/templatefrontoffice/forum/{id}', name: 'app_forum_show')]
    public function show(int $id, CategorieForumRepository $categorieForumRepository): Response
    {
        // Récupérer la catégorie de forum via son ID
        $categorieForum = $categorieForumRepository->find($id);
    
        if (!$categorieForum) {
            throw $this->createNotFoundException('Catégorie de forum non trouvée');
        }
    
        // Récupérer les posts associés à cette catégorie
        $posts = $categorieForum->getPosts();
    
        // Lire le fichier JSON des moyennes
        $filePath = '../public/rating.json';
        $ratings = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    
        // Associer chaque post avec sa moyenne
        $postRatings = [];
        foreach ($posts as $post) {
            $postId = $post->getId();
            $postRatings[$postId] = $ratings[$postId]['average'] ?? null; // Met null si pas de moyenne
        }
    
        // Trouver le post avec le plus de commentaires
        $postWithMostComments = null;
        $maxComments = 0;
    
        foreach ($posts as $post) {
            $commentCount = count($post->getCommentaires());
            if ($commentCount > $maxComments) {
                $maxComments = $commentCount;
                $postWithMostComments = $post;
            }
        }
    
        return $this->render('templatefrontoffice/forum_show.html.twig', [
            'categorie_forum' => $categorieForum,
            'posts' => $posts,
            'id' => $id,
            'moyenne' => $postRatings,
            'post_with_most_comments' => $postWithMostComments, // Passer le post avec le plus de commentaires
        ]);
    }}
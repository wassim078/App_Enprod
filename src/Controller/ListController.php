<?php
namespace App\Controller;

use App\Repository\CategorieForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Forum;
use App\Entity\Post;
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

    #[Route('/templatefrontoffice/forum/{id}', name: 'app_forum_show')]
    public function show(int $id, CategorieForumRepository $categorieForumRepository): Response
    {
        $categorieForum = $categorieForumRepository->find($id);

        if (!$categorieForum) {
            throw $this->createNotFoundException('Forum non trouvé');
        }

        return $this->render('templatefrontoffice/forum_show.html.twig', [
            'categorie_forum' => $categorieForum,
        ]);
    }

    
}

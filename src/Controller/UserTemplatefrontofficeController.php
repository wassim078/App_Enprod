<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserTemplatefrontofficeController extends AbstractController{
    #[Route('/templatefrontoffice/{page}', name: 'app_templatefrontoffice',defaults:['page' => 'index'])]
    public function index(String $page): Response
    {
        
        return $this->render('templatefrontoffice/'. $page . '.html.twig', [
            'controller_name' => 'ClientTemplatefrontofficeController',
        ]);
    }

    #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    public function index1(CategorieForumRepository $categorieForumRepository): Response
    {
        // On récupère toutes les catégories du forum
        $categorieForums = $categorieForumRepository->findAll();

        // On rend la vue et on passe les catégories à celle-ci
        return $this->render('templatefrontoffice/forum.html.twig', [
            'categorie_forums' => $categorieForums,  // On passe la variable avec la bonne clé
        ]);
    }




    
}

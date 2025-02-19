<?php
namespace App\Controller;


use App\Entity\Annonce;
use App\Entity\Category;


use App\Repository\AnnonceRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

final class UserTemplatefrontofficeController extends AbstractController
{
#[Route('/templatefrontoffice/{page}', name: 'app_templatefrontoffice', defaults:['page' => 'index'])]
public function index(String $page): Response
{
return $this->render('templatefrontoffice/'.$page.'.html.twig', [
'controller_name' => 'UserTemplatefrontofficeController',
]);
}

    #[Route('/user/annonce', name: 'app_annonce_index_front', methods: ['GET'])]
    public function indexFront(AnnonceRepository $annonceRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        // Récupère l'ID de la catégorie depuis les paramètres de la requête (GET)
        $categoryId = $request->query->get('category');

        // Si un ID de catégorie est spécifié, filtre les annonces par catégorie
        if ($categoryId) {
            $annonces = $annonceRepository->findBy(['category' => $categoryId]);
        } else {
            // Sinon, récupère toutes les annonces
            $annonces = $annonceRepository->findAll();
        }

        // Récupère toutes les catégories pour les afficher dans le filtre
        $categories = $categoryRepository->findAll();

        return $this->render('templatefrontoffice/annonce.html.twig', [
            'annonces' => $annonces,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
        ]);
    }
}

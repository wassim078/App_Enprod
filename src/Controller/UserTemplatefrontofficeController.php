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
use Knp\Component\Pager\PaginatorInterface;

final class UserTemplatefrontofficeController extends AbstractController
{
    #[Route('/templatefrontoffice/{page}', name: 'app_templatefrontoffice', defaults: ['page' => 'index'])]
    public function index(String $page): Response
    {
        return $this->render('templatefrontoffice/' . $page . '.html.twig', [
            'controller_name' => 'UserTemplatefrontofficeController',
        ]);
    }

   
     // Recherche des annonces par mot-clé
     
    #[Route('/user/annonce/search', name: 'app_annonce_search', methods: ['GET'])]
    public function search(
        AnnonceRepository $annonceRepository, 
        CategoryRepository $categoryRepository,
        Request $request, 
        PaginatorInterface $paginator
    ): Response {
        // Récupère le terme de recherche depuis la requête
        $searchTerm = $request->query->get('q');
        
        // Récupération des paramètres de filtrage
        $categoryId = $request->query->get('category');
        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');
        $minWeight = $request->query->get('min_weight');
        $maxWeight = $request->query->get('max_weight');
        $sortBy = $request->query->get('sort_by', 'a.id');
        
        // Initialisation du QueryBuilder
        $queryBuilder = $annonceRepository->createQueryBuilder('a');
        
        // Applique le filtre de recherche si un terme est fourni
        if ($searchTerm) {
            $queryBuilder->where('a.titre LIKE :searchTerm OR a.description LIKE :searchTerm')
                        ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        
        // Filtre par catégorie si spécifiée
        if ($categoryId) {
            // Utilise andWhere au lieu de where pour ne pas écraser la condition de recherche
            $condition = $searchTerm ? 'andWhere' : 'where';
            $queryBuilder->$condition('a.category = :category')
                        ->setParameter('category', $categoryId);
        }
        
        // Applique les filtres de prix
        if ($minPrice) {
            $queryBuilder->andWhere('a.prix >= :minPrice')
                        ->setParameter('minPrice', $minPrice);
        }
        if ($maxPrice) {
            $queryBuilder->andWhere('a.prix <= :maxPrice')
                        ->setParameter('maxPrice', $maxPrice);
        }
        
        // Applique les filtres de poids
        if ($minWeight) {
            $queryBuilder->andWhere('a.poids >= :minWeight')
                        ->setParameter('minWeight', $minWeight);
        }
        if ($maxWeight) {
            $queryBuilder->andWhere('a.poids <= :maxWeight')
                        ->setParameter('maxWeight', $maxWeight);
        }
        
        // Applique le tri si valide
        if (in_array($sortBy, ['a.prix', 'a.poids', 'a.category', 'a.titre', 'a.id'])) {
            $queryBuilder->orderBy($sortBy, 'ASC');
        }
        
        // Récupère la page actuelle (1 par défaut)
        $page = $request->query->getInt('page', 1);
        
        // Pagination des résultats (4 annonces par page)
        $annonces = $paginator->paginate(
            $queryBuilder,
            $page,
            4
        );
        
        // Récupère toutes les catégories pour le formulaire de filtre
        $categories = $categoryRepository->findAll();
        
        // Rend la vue avec les résultats et les paramètres de filtre
        return $this->render('templatefrontoffice/annonce.html.twig', [
            'annonces' => $annonces,
            'categories' => $categories,
            'searchTerm' => $searchTerm,
            'selectedCategoryId' => $categoryId,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minWeight' => $minWeight,
            'maxWeight' => $maxWeight,
            'sortBy' => $sortBy,
        ]);
    }

    #[Route('/user/annonce', name: 'app_annonce_index_front', methods: ['GET'])]
    public function indexFront(
        AnnonceRepository $annonceRepository, 
        CategoryRepository $categoryRepository, 
        Request $request, 
        PaginatorInterface $paginator
    ): Response {
        // Récupération des paramètres GET pour le filtrage
         // Récupération des paramètres de filtrage
         $categoryId = $request->query->get('category');
         $minPrice = $request->query->get('min_price');
         $maxPrice = $request->query->get('max_price');
         $minWeight = $request->query->get('min_weight');
         $maxWeight = $request->query->get('max_weight');
         $sortBy = $request->query->get('sort_by', 'a.id'); // Tri par défaut
 
        // Construction de la requête en fonction de la catégorie sélectionnée
        $queryBuilder = $annonceRepository->createQueryBuilder('a');
        
        if ($categoryId) {
            $queryBuilder->where('a.category = :category')->setParameter('category', $categoryId);
        }

                // Filtrage par prix
                if ($minPrice) {
                    $queryBuilder->andWhere('a.prix >= :minPrice')
                                 ->setParameter('minPrice', $minPrice);
                }
                if ($maxPrice) {
                    $queryBuilder->andWhere('a.prix <= :maxPrice')
                                 ->setParameter('maxPrice', $maxPrice);
                }
        
                // Filtrage par poids
                if ($minWeight) {
                    $queryBuilder->andWhere('a.poids >= :minWeight')
                                 ->setParameter('minWeight', $minWeight);
                }
                if ($maxWeight) {
                    $queryBuilder->andWhere('a.poids <= :maxWeight')
                                 ->setParameter('maxWeight', $maxWeight);
                }
                if (in_array($sortBy, ['a.prix', 'a.poids', 'a.category'])) {
                    $queryBuilder->orderBy($sortBy, 'ASC'); // Tri ascendant par défaut
                }        

        // Récupère la page actuelle depuis la requête (1 par défaut)
        $page = $request->query->getInt('page', 1);

        // Pagination des annonces (4 annonces par page)
        $annonces = $paginator->paginate(
            $queryBuilder,  // Requête DQL
            $page,          // Numéro de la page
            4            // Nombre d'éléments par page
        );

        // Récupère toutes les catégories pour les afficher dans le filtre
        $categories = $categoryRepository->findAll();

        return $this->render('templatefrontoffice/annonce.html.twig', [
            'annonces' => $annonces,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minWeight' => $minWeight,
            'maxWeight' => $maxWeight,
            'sortBy' => $sortBy,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplatebackofficeController extends AbstractController{
    #[Route('/templatebackoffice/{page}', name: 'app_templatebackoffice', defaults: ['page' => 'index'])]
    public function index(String $page, AnnonceRepository $annonceRepository, CategoryRepository $categoryRepository): Response
    {
        // Retrieve all annonces and categories
        $annonces = $annonceRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('templatebackoffice/' . $page . '.html.twig', [
            'controller_name' => 'TemplatebackofficeController',
            'annonces' => $annonces,
            'categories' => $categories,
        ]);
    }


}

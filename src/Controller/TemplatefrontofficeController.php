<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplatefrontofficeController extends AbstractController{
    #[Route('/templatefrontoffice/{page}', name: 'app_templatefrontoffice',defaults:['page' => 'index'])]
    public function index(String $page): Response
    {
        return $this->render('templatefrontoffice/'. $page . '.html.twig', [
            'controller_name' => 'TemplatefrontofficeController',
        ]);
    }
    
}

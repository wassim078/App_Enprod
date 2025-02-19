<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplatebackofficeController extends AbstractController{
    #[Route('/templatebackoffice/{page}', name: 'app_templatebackoffice',defaults:['page' => 'index'])]
    public function index(String $page): Response
    {
        return $this->render('templatebackoffice/'.$page.'.html.twig', [
            'controller_name' => 'TemplatebackofficeController',
        ]);
    }
}

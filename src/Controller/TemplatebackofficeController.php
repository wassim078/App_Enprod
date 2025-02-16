<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TemplatebackofficeController extends AbstractController{
    #[Route('/templatebackoffice/{page}', name: 'app_templatebackoffice',defaults:['page' => 'index'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(String $page): Response
    {
        return $this->render('templatebackoffice/'.$page.'.html.twig', [
            'controller_name' => 'TemplatebackofficeController',
        ]);
    }
}

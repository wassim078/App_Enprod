<?php

namespace App\Controller\backoffice;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Entity\Post;
use App\Form\UserForm;
use App\Form\UserAddForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;





final class BackAnnonceController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    



    #[Route('/templatebackoffice/annonce', name: 'admin_annonce_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function annonceManagement(EntityManagerInterface $entityManager): Response
    {
       
        $annonces = $entityManager->getRepository(Annonce::class)->findAll();

        return $this->render('templatebackoffice/annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }



}
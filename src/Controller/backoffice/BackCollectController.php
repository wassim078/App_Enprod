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





final class BackCollectController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    






                    //Collect-managements

    #[Route('/templatebackoffice/collect', name: 'admin_collect_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function collectManagement(EntityManagerInterface $entityManager): Response
    {
        
        $collects = $entityManager->getRepository(Collect::class)->findAll();

        return $this->render('templatebackoffice/collect/index.html.twig', [
            'collects' => $collects,
        ]);
    }
















                //Forum-managements

  

}

















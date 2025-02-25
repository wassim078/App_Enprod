<?php

namespace App\Controller\frontoffice;
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





final class FrontForumController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    


                //Forum-managements

    #[Route('/templatefrontoffice/forum', name: 'user_forum_management')]


    public function forum(EntityManagerInterface $entityManager): Response
    {
       
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('templatefrontoffice/forum/index.html.twig', [
            'posts' => $posts,
        ]);
    }

}

















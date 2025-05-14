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
use App\Repository\CategorieForumRepository;







final class BackUserController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }






#[Route('/templatebackoffice/user', name: 'admin_user_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function userManagement(EntityManagerInterface $entityManager): Response
    {
        
        $users = array_filter($entityManager->getRepository(User::class)->findAll(),function ($user) {
            return !in_array('ROLE_ADMIN', $user->getRoles());
        });
        return $this->render('templatebackoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }
    

            //User-managements (Editer)

            #[Route('/templatebackoffice/user/new', name: 'app_user_new', methods: ['GET', 'POST'])]
            public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
            {
                $user = new User();
                $form = $this->createForm(UserAddForm::class, $user);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid()) {


                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    $imageFile = $form->get('image')->getData();
        
                        if ($imageFile) {
                             $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
                            // Move the file to the uploads directory
                            $imageFile->move(
                            $this->getParameter('images_directory'), // Define this in services.yaml
                            $newFilename
                            );
            
                            $user->setImage($newFilename);
                             }



                    $entityManager->persist($user);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('admin_user_management', [], Response::HTTP_SEE_OTHER);
                }
        
                return $this->render('templatebackoffice/user/new.html.twig', [
                    'form' => $form,
                ]);
            }
        
            #[Route('/templatebackoffice/user/{id}', name: 'app_user_show', methods: ['GET'])]
            public function show(User $user): Response
            {
                return $this->render('templatebackoffice/user/show.html.twig', [
                    'user' => $user,
                ]);
            }
        
            #[Route('/templatebackoffice/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
            public function edit(Request $request, User $user, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
            {
                $form = $this->createForm(UserForm::class, $user);
                $form->handleRequest($request);
        

           

                if ($form->isSubmitted() && $form->isValid()) {

                    

                    $plainPassword = $form->get('plainPassword')->getData();
                    
                    if (!empty($plainPassword)) {
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    }

                    
                    $imageFile = $form->get('image')->getData();
        
                        if ($imageFile) {
                             $newFilename = uniqid().'.'.$imageFile->guessExtension();
            
                            // Move the file to the uploads directory
                            $imageFile->move(
                            $this->getParameter('images_directory'), // Define this in services.yaml
                            $newFilename
                            );
            
                            $user->setImage($newFilename);
                             }




                    $entityManager->flush();
        
                    return $this->redirectToRoute('admin_user_management', [], Response::HTTP_SEE_OTHER);
                }
        
                return $this->render('templatebackoffice/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }
        
            #[Route('/templatebackoffice/user/{id}', name: 'app_user_delete', methods: ['POST'])]
            public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
            {
                //if ($this->isCsrfTokenValid('delete' . $user->getId(),  $request->request->get('_token'))) {
                    $entityManager->remove($user);
                    $entityManager->flush();
                //}
        
                return $this->redirectToRoute('admin_user_management', [], Response::HTTP_SEE_OTHER);
            }







        }
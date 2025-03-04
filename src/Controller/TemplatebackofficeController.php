<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Entity\Post;
use App\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CollectType;




final class TemplatebackofficeController extends AbstractController{






    #[Route('/templatebackoffice', name: 'admin_dashborad')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashborad(EntityManagerInterface $entityManager): Response
    {
        

        return $this->render('templatebackoffice/index.html.twig');
    }
    











            //User-managements (Consulter)

    #[Route('/templatebackoffice/user-management', name: 'admin_user_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function userManagement(EntityManagerInterface $entityManager): Response
    {
        
        $users = array_filter($entityManager->getRepository(User::class)->findAll(),function ($user) {
            return !in_array('ROLE_ADMIN', $user->getRoles());
        });
        return $this->render('templatebackoffice/user-management.html.twig', [
            'users' => $users,
        ]);
    }
    

            //User-managements (Editer)

    #[Route('/templatebackoffice/manageusers/edit/{id}', name: 'admin_edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUser(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Fetch the user by ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Create and handle the form
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated user to the database
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('admin_user_management');
        }

        return $this->render('templatebackoffice/user/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }


















                    //Annonce-managements

    #[Route('/templatebackoffice/shop-management', name: 'admin_shop_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function annonceManagement(EntityManagerInterface $entityManager): Response
    {
       
        $annonces = $entityManager->getRepository(Annonce::class)->findAll();

        return $this->render('templatebackoffice/shop-management.html.twig', [
            'annonces' => $annonces,
        ]);
    }

















                    //Collect-managements
                    #[Route('/templatebackoffice/collect-management', name: 'admin_collect_management')]
                    #[IsGranted('ROLE_ADMIN')]
                    public function collectManagement(EntityManagerInterface $entityManager): Response
                    {
                        // Fetch all Collect entities from the database
                        $collects = $entityManager->getRepository(Collect::class)->findAll();
                
                        // Pass the collects data to the Twig template
                        return $this->render('templatebackoffice/collect-management.html.twig', [
                            'collects' => $collects,
                        ]);
                    }
                
                    /**
                     * Edit a specific Collect entity.
                     */
                    #[Route('/templatebackoffice/collect/{id}/edit', name: 'admin_collect_edit')]
                    #[IsGranted('ROLE_ADMIN')]
                    public function edit(Request $request, EntityManagerInterface $entityManager, Collect $collect): Response
                    {
                        // Create the form and bind it to the existing Collect entity
                        $form = $this->createForm(CollectType::class, $collect);
                
                        // Handle the submitted form data
                        $form->handleRequest($request);
                        if ($form->isSubmitted() && $form->isValid()) {
                            $entityManager->flush(); // Persist the changes to the database
                            $this->addFlash('success', 'Collect updated successfully.');
                            return $this->redirectToRoute('admin_collect_management'); // Redirect back to the management page
                        }
                
                        // Render the edit form template
                        return $this->render('templatebackoffice/collect-edit.html.twig', [
                            'form' => $form->createView(),
                            'collect' => $collect,
                        ]);
                    }
                
                    /**
                     * Delete a specific Collect entity.
                     */
                    #[Route('/templatebackoffice/collect/{id}/delete', name: 'admin_collect_delete', methods: ['POST'])]
                    #[IsGranted('ROLE_ADMIN')]
                    public function delete(Request $request, EntityManagerInterface $entityManager, Collect $collect): Response
                    {
                        // Ensure the request includes a valid CSRF token
                        if ($this->isCsrfTokenValid('delete' . $collect->getId(), $request->request->get('_token'))) {
                            $entityManager->remove($collect); // Remove the entity
                            $entityManager->flush(); // Persist the deletion to the database
                            $this->addFlash('success', 'Collect deleted successfully.');
                        }
                
                        return $this->redirectToRoute('admin_collect_management'); // Redirect back to the management page
                    }
                    
















                //Forum-managements

    #[Route('/templatebackoffice/forum-management', name: 'admin_forum_management')]
    #[IsGranted('ROLE_ADMIN')]

    public function forum(EntityManagerInterface $entityManager): Response
    {
       
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('templatebackoffice/forum-management.html.twig', [
            'posts' => $posts,
        ]);
    }















}

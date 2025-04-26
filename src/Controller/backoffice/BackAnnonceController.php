<?php

namespace App\Controller\backoffice;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Entity\Post;
use App\Form\AnnonceType;
use App\Form\UserForm;
use App\Form\UserAddForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        $annonces = $entityManager->getRepository(Annonce::class)->findAllWithCategoryAndUser();

        return $this->render('templatebackoffice/annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }
// Add these methods to BackAnnonceController
    #[Route('/templatebackoffice/annonce/edit/{id}', name: 'admin_annonce_edit', methods: ['GET', 'POST'])]
    public function editAnnonce(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $uploadsDirectory = $this->getParameter('uploads_directory');

                try {
                    $imageFile->move($uploadsDirectory, $newFilename);
                    $annonce->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Could not update image');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Annonce updated successfully');
            return $this->redirectToRoute('admin_annonce_management');
        }

        return $this->render('templatebackoffice/annonce/edit.html.twig', [
            'form' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    #[Route('/templatebackoffice/annonce/delete/{id}', name: 'admin_annonce_delete', methods: ['POST'])]
    public function deleteAnnonce(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
            $this->addFlash('success', 'Annonce deleted successfully');
        }

        return $this->redirectToRoute('admin_annonce_management');
    }


}
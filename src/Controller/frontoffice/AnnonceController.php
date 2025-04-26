<?php

namespace App\Controller\frontoffice;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/templatefrontoffice/annonce/myAnnoncements', name: 'my_annoncements')]
    public function myAnnoncements(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $annonces = $entityManager->getRepository(Annonce::class)
            ->findBy(['user' => $this->getUser()], ['id' => 'DESC']);

        return $this->render('templatefrontoffice/annonce/my_annoncements.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/templatefrontoffice/annonce/new', name: 'annonce_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $annonce = new Annonce();
        $annonce->setUser($this->getUser());

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $uploadsDirectory = $this->getParameter('uploads_directory');

                try {
                    $imageFile->move($uploadsDirectory, $newFilename);
                    $annonce->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Could not save image file.');
                }
            }

            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce created successfully!');
            return $this->redirectToRoute('my_annoncements');
        }

        return $this->render('templatefrontoffice/annonce/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/templatefrontoffice/annonce/edit/{id}', name: 'annonce_edit')]
    public function edit(
        Request $request,
        Annonce $annonce,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Verify user owns the annonce
        if ($annonce->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this announcement');
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $uploadsDirectory = $this->getParameter('uploads_directory');

                try {
                    // Delete old image if exists
                    if ($annonce->getImage()) {
                        $oldImage = $uploadsDirectory.'/'.$annonce->getImage();
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }

                    $imageFile->move($uploadsDirectory, $newFilename);
                    $annonce->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Could not update image file.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Annonce updated successfully!');
            return $this->redirectToRoute('my_annoncements');
        }

        return $this->render('templatefrontoffice/annonce/edit.html.twig', [
            'form' => $form->createView(),
            'annonce' => $annonce,
        ]);
    }

    #[Route('/templatefrontoffice/annonce/delete/{id}', name: 'annonce_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Annonce $annonce,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($annonce->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not own this announcement');
        }

        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            // Delete associated image
            $uploadsDirectory = $this->getParameter('uploads_directory');
            if ($annonce->getImage()) {
                $imagePath = $uploadsDirectory.'/'.$annonce->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce deleted successfully!');
        }

        return $this->redirectToRoute('my_annoncements');
    }
}
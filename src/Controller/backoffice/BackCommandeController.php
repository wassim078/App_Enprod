<?php

namespace App\Controller\backoffice;
use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Form\CommandeBackType;
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
use App\Repository\AnnonceRepository;




final class BackCommandeController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    // src/Controller/backoffice/BackCommandeController.php
    #[Route('/templatebackoffice/commande', name: 'admin_commande_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager->getRepository(Commande::class)->findAllWithUser();

        return $this->render('templatebackoffice/commande/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route('/commande/delete/{id}', name: 'admin_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
            $this->addFlash('success', 'Commande deleted successfully');
        }

        return $this->redirectToRoute('admin_commande_management');
    }

    // src/Controller/backoffice/BackCommandeController.php
    #[Route('/commande/edit/{id}', name: 'admin_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager, AnnonceRepository $annonceRepo): Response
    {
        $form = $this->createForm(CommandeBackType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Commande updated successfully');
            return $this->redirectToRoute('admin_commande_management');
        }

        return $this->render('templatebackoffice/commande/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
            'annonce_repository' => $annonceRepo
        ]);
    }









}

















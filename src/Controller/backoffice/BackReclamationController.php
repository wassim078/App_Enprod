<?php

namespace App\Controller\backoffice;
use App\Entity\Reclamation;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Form\ReclamationBackType;
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





final class BackReclamationController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    #[Route('/templatebackoffice/reclamation', name: 'admin_reclamation_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager->getRepository(Reclamation::class)->findAllWithUser();

        return $this->render('templatebackoffice/reclamation/index.html.twig', [
            'reclamations' => $reclamations
        ]);
    }

    #[Route('/reclamation/delete/{id}', name: 'admin_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Reclamation deleted successfully');
        }

        return $this->redirectToRoute('admin_reclamation_management');
    }

    #[Route('/reclamation/edit/{id}', name: 'admin_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationBackType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Reclamation updated successfully');
            return $this->redirectToRoute('admin_reclamation_management');
        }

        return $this->render('templatebackoffice/reclamation/edit.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation
        ]);
    }








}

















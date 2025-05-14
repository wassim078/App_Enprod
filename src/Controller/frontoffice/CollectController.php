<?php

// src/Controller/CollectController.php
namespace App\Controller\frontoffice;

use App\Entity\Collect;
use App\Form\CollectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/collect')]
class CollectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    #[Route('/my-collects', name: 'collect_list')]
    public function list(): Response
    {
        $user = $this->security->getUser();
        $collects = $this->em->getRepository(Collect::class)->findByUser($user);

        return $this->render('templatefrontoffice/collect/list.html.twig', [
            'collects' => $collects
        ]);
    }

    #[Route('/create', name: 'collect_create')]
    public function create(Request $request): Response
    {
        $collect = new Collect();
        $form = $this->createForm(CollectType::class, $collect);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $collect->setUser($this->security->getUser());

            $this->em->persist($collect);
            $this->em->flush();

            $this->addFlash('success', 'Collect created successfully!');
            return $this->redirectToRoute('collect_list');
        }

        return $this->render('templatefrontoffice/collect/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // src/Controller/CollectController.php
    #[Route('/edit/{id}', name: 'collect_edit')]
    public function edit(Request $request, Collect $collect): Response
    {
        // Check if user owns the collect
        if ($collect->getUser() !== $this->security->getUser()) {
            $this->addFlash('error', 'You are not authorized to edit this collect');
            return $this->redirectToRoute('collect_list');
        }

        $form = $this->createForm(CollectType::class, $collect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Collect updated successfully!');
            return $this->redirectToRoute('collect_list');
        }

        return $this->render('templatefrontoffice/collect/edit.html.twig', [
            'form' => $form->createView(),
            'collect' => $collect
        ]);
    }

    #[Route('/delete/{id}', name: 'collect_delete', methods: ['POST'])]
    public function delete(Request $request, Collect $collect): Response
    {
        // Check if user owns the collect
        if ($collect->getUser() !== $this->security->getUser()) {
            $this->addFlash('error', 'You are not authorized to delete this collect');
            return $this->redirectToRoute('collect_list');
        }

        if ($this->isCsrfTokenValid('delete'.$collect->getId(), $request->request->get('_token'))) {
            $this->em->remove($collect);
            $this->em->flush();
            $this->addFlash('success', 'Collect deleted successfully!');
        }

        return $this->redirectToRoute('collect_list');
    }

    #[Route('/collects', name: 'collect_all')]
    public function allCollects(): Response
    {
        $collects = $this->em->getRepository(Collect::class)->findAllCollects();

        return $this->render('templatefrontoffice/collect/all.html.twig', [
            'collects' => $collects
        ]);
    }

}
<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Form\CollectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CollectRepository;

#[Route('/templatefrontoffice/collect')]
class CollectController extends AbstractController
{ 
    #[Route('/', name: 'app_collect_index', methods: ['GET'])]
    public function index(CollectRepository $collectRepository): Response
    {
        
        return $this->render('templatefrontoffice/collect/index.html.twig', [
            'collects' => $collectRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_collect_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collect = new Collect();
        $collectForm = $this->createForm(CollectType::class, $collect);
        $collectForm->handleRequest($request);

        if ($collectForm->isSubmitted() && $collectForm->isValid()) {
            $entityManager->persist($collect);
            $entityManager->flush();
            $this->addFlash('success', 'Collection scheduled!');
            return $this->redirectToRoute('app_collect_new');
        }

        return $this->render('templatefrontoffice/collect/collect.html.twig', [
            'collectForm' => $collectForm->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_collect_show', methods: ['GET'])]
    public function show(Collect $collect): Response
    {
        return $this->render('collect/show.html.twig', [
            'collect' => $collect,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collect_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collect $collect, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectType::class, $collect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_collect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collect/edit.html.twig', [
            'collect' => $collect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collect_delete', methods: ['POST'])]
    public function delete(Request $request, Collect $collect, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collect->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collect);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collect_index', [], Response::HTTP_SEE_OTHER);
    }
}
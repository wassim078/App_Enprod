<?php

namespace App\Controller;

use App\Entity\CategorieCollect;
use App\Form\CategorieCollectType;
use App\Repository\CategorieCollectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie/collect')]
final class CategorieCollectController extends AbstractController
{
    #[Route(name: 'app_categorie_collect_index', methods: ['GET'])]
    public function index(CategorieCollectRepository $categorieCollectRepository): Response
    {
        return $this->render('categorie_collect/index.html.twig', [
            'categorie_collects' => $categorieCollectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_collect_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieCollect = new CategorieCollect();
        $form = $this->createForm(CategorieCollectType::class, $categorieCollect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieCollect);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_collect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_collect/new.html.twig', [
            'categorie_collect' => $categorieCollect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_collect_show', methods: ['GET'])]
    public function show(CategorieCollect $categorieCollect): Response
    {
        return $this->render('categorie_collect/show.html.twig', [
            'categorie_collect' => $categorieCollect,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_collect_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieCollect $categorieCollect, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieCollectType::class, $categorieCollect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_collect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_collect/edit.html.twig', [
            'categorie_collect' => $categorieCollect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_collect_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieCollect $categorieCollect, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieCollect->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieCollect);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_collect_index', [], Response::HTTP_SEE_OTHER);
    }
}

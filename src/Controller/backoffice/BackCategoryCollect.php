<?php

namespace App\Controller\backoffice;

use App\Entity\CategorieCollect;
use App\Form\CollectCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BackCategoryCollect extends AbstractController
{
    #[Route('/templatebackoffice/category_collect', name: 'admin_category_collect')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieCollect::class)->findAll();

        return $this->render('templatebackoffice/categorie_collect/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category_collect/new', name: 'admin_category_collect_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new CategorieCollect();
        $form = $this->createForm(CollectCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin_category_collect');
        }

        return $this->render('templatebackoffice/categorie_collect/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category_collect/edit/{id}', name: 'admin_category_collect_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, CategorieCollect $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin_category_collect');
        }

        return $this->render('templatebackoffice/categorie_collect/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/category_collect/delete/{id}', name: 'admin_category_collect_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, CategorieCollect $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category deleted successfully');
        }

        return $this->redirectToRoute('admin_category_collect');
    }
}

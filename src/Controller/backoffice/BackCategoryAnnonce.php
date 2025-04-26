<?php

namespace App\Controller\backoffice;

use App\Entity\CategorieAnnonce;
use App\Entity\Collect;
use App\Form\CategoryAnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BackCategoryAnnonce extends AbstractController
{


    #[Route('/templatebackoffice/category_annonce', name: 'admin_category_annonce')]
    #[IsGranted('ROLE_ADMIN')]
    public function categoryManagement(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieAnnonce::class)->findAll();

        return $this->render('templatebackoffice/category_annonce/index.html.twig', [
            'categories' => $categories,
        ]);
    }


    // src/Controller/backoffice/BackCategoryAnnonce.php
    #[Route('/category_annonce/delete/{id}', name: 'admin_category_annonce_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, CategorieAnnonce $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category deleted successfully');
        }

        return $this->redirectToRoute('admin_category_annonce');
    }

    #[Route('/category_annonce/edit/{id}', name: 'admin_category_annonce_edit')]
    public function editCategory(Request $request, CategorieAnnonce $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryAnnonceType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin_category_annonce');
        }

        return $this->render('templatebackoffice/category_annonce/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }


    #[Route('/category_annonce/new', name: 'admin_category_annonce_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new CategorieAnnonce();
        $form = $this->createForm(CategoryAnnonceType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin_category_annonce');
        }

        return $this->render('templatebackoffice/category_annonce/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
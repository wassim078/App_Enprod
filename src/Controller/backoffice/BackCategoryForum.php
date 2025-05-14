<?php

namespace App\Controller\backoffice;

use App\Entity\CategorieForum;
use App\Form\ForumCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BackCategoryForum extends AbstractController
{
    #[Route('/templatebackoffice/category_forum', name: 'admin_category_forum')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(CategorieForum::class)->findAllOrdered();

        return $this->render('templatebackoffice/category_forum/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category_forum/new', name: 'admin_category_forum_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new CategorieForum();
        $form = $this->createForm(ForumCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin_category_forum');
        }

        return $this->render('templatebackoffice/category_forum/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category_forum/edit/{id}', name: 'admin_category_forum_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, CategorieForum $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin_category_forum');
        }

        return $this->render('templatebackoffice/category_forum/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/category_forum/delete/{id}', name: 'admin_category_forum_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, CategorieForum $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category deleted successfully');
        }

        return $this->redirectToRoute('admin_category_forum');
    }
}
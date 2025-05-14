<?php

namespace App\Controller;

use App\Entity\CategorieForum;
use App\Repository\CategorieForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\CategorieType; // Bon import




#[Route('/templatebackoffice/category/forum')]
final class CategoryForumController extends AbstractController{
    
   #[Route(name: 'app_category_forum_index', methods: ['GET'])]
public function index(CategorieForumRepository $categorieForumRepository): Response
{
    // Assuming there is a repository method to fetch categories.
    $categories = $categorieForumRepository->findAll(); // Replace with appropriate query if needed

    return $this->render('templatebackoffice/category_forum/index.html.twig', [
        'categories' => $categories,
        'categorie_forums' => $categorieForumRepository->findAll(),
    ]);
}

   

    #[Route('/new', name: 'app_category_forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieForum = new  CategorieForum();
        $form = $this->createForm(CategorieType::class, $categorieForum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieForum);
            $entityManager->flush();

            return $this->redirectToRoute('app_category_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_forum/new.html.twig', [
            'categorie_forum' => $categorieForum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_forum_show', methods: ['GET'])]
    public function show(CategorieForum $categorieForum): Response
    {
        return $this->render('category_forum/show.html.twig', [
            'categorie_forum' => $categorieForum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_forum_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieForum $categorieForum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorieForum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_category_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_forum/edit.html.twig', [
            'categorie_forum' => $categorieForum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_forum_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieForum $categorieForum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieForum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieForum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller\backoffice;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Collect;
use App\Form\CollectType;
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





final class BackCollectController extends AbstractController{

    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    






                    //Collect-managements

    // In BackCollectController
    #[Route('/templatebackoffice/collect', name: 'admin_collect_management')]
    #[IsGranted('ROLE_ADMIN')]
    public function collectManagement(EntityManagerInterface $entityManager): Response
    {
        // Use the custom repository method to get collects with joined user data
        $collects = $entityManager->getRepository(Collect::class)->findAllCollects();

        return $this->render('templatebackoffice/collect/index.html.twig', [
            'collects' => $collects,
        ]);
    }



    // src/Controller/backoffice/BackCollectController.php

// Add this method to the BackCollectController class
    #[Route('/templatebackoffice/collect/delete/{id}', name: 'admin_collect_delete', methods: ['POST'])]
    public function deleteCollect(Request $request, Collect $collect, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collect->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collect);
            $entityManager->flush();
            $this->addFlash('success', 'Collect deleted successfully.');
        }

        return $this->redirectToRoute('admin_collect_management');
    }



    // Add this to BackCollectController
    #[Route('/templatebackoffice/collect/edit/{id}', name: 'admin_collect_edit')]
    public function editCollect(Request $request, Collect $collect, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectType::class, $collect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Collect updated successfully!');
            return $this->redirectToRoute('admin_collect_management');
        }

        return $this->render('templatebackoffice/collect/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
















                //Forum-managements

  

}

















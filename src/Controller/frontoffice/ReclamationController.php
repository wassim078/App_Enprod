<?php

namespace App\Controller\frontoffice;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ReclamationController extends AbstractController
{
    #[Route('/complaint/create', name: 'complaint_create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setUser($security->getUser());

            // VichUploader handles the file automatically
            $em->persist($reclamation);
            $em->flush();

            $this->addFlash('success', 'Complaint created successfully!');
            return $this->redirectToRoute('complaint_list');
        }

        return $this->render('templatefrontoffice/reclamation/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/my-complaints', name: 'complaint_list')]
    public function list(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        $complaints = $em->getRepository(Reclamation::class)->findBy(['user' => $user]);

        return $this->render('templatefrontoffice/reclamation/list.html.twig', [
            'complaints' => $complaints
        ]);
    }


    #[Route('/delete/{id}', name: 'complaint_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Reclamation $reclamation,
        EntityManagerInterface $em
    ): Response {
        // Verify CSRF token
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            // Optional: Delete associated file
            if ($reclamation->getFileName()) {
                $filePath = $this->getParameter('reclamation_files_directory').'/'.$reclamation->getFileName();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $em->remove($reclamation);
            $em->flush();

            $this->addFlash('success', 'Complaint deleted successfully');
        }

        return $this->redirectToRoute('complaint_list');
    }

}
<?php

namespace App\Controller\frontoffice;

use Dompdf\Dompdf;
use App\Entity\User;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationController extends AbstractController
{
    #[Route('templatefrontoffice/reclamation', name: 'reclamation')]
    public function index(Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $reclamations = $reclamationRepository->findAll();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
            'form' => $form->createView(),
            'reclamation' => $reclamations
        ]);
    }
    #[Route('/edit/{id}', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }
    
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                
             
                $reclamation->setFile($fileName);
            }
            $entityManager->flush();
            return $this->redirectToRoute('reclamation', [], Response::HTTP_SEE_OTHER);
        } 
    
        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

   
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form->get('file')->getData()) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
                $reclamation->setFile($fileName);
            }
            $entityManager->persist($reclamation);
            $entityManager->flush();
            return $this->redirectToRoute('reclamation');
        }

        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_reclamation_delete', methods: ['GET'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->find(Reclamation::class, $id);
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('admin_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
     #[Route('/rapport/{reclamationId}', name: 'rapport')]
    public function generatePdfAction($reclamationId, EntityManagerInterface $entityManager)
    {
        $reclamation = $entityManager->find(Reclamation::class, $reclamationId);
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        $imagePath = $this->getParameter('upload_directory') . '/' . $reclamation->getFile();
        $userName = $reclamation->getUser() ? $reclamation->getUser()->getNom() : 'Inconnu';

        function imageToDataUrl(string $filename): string
        {
            if (!file_exists($filename)) {
                return '';
            }
            $mime = mime_content_type($filename);
            return "data:{$mime};base64," . base64_encode(file_get_contents($filename));
        }

        $html = $this->renderView('reclamation/rapport.html.twig', [
            'reclamation' => $reclamation,
            'imageDataUrl' => file_exists($imagePath) ? imageToDataUrl($imagePath) : '',
            'userName' => $userName,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, ['Content-Type' => 'application/pdf']);
    }

}

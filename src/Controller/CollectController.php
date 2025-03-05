<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Collect;
use App\Form\CollectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CollectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

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

    #[Route('/search', name: 'app_collect_index_search', methods: ['GET'])]
    public function jsSearch(CollectRepository $collectRepository, SerializerInterface $serializer): JsonResponse
    {
        $input = $_GET['query'] ?? '';
        $data = $collectRepository->search($input);
        $json = $serializer->serialize($data, 'json');

        return new JsonResponse(data: $json, json: true);
    }

    
    #[Route('/collect', name: 'collect_list')]
    public function list(CollectsRepository $collectsRepo): Response
    {
        $collects = $collectsRepo->findAllWithCategories();
        
        // Group collects by category
        $grouped = [];
        foreach ($collects as $collect) {
            $category = $collect->getCategorieCollect();
            if ($category) {
                $categoryName = $category->getNom();
                $grouped[$categoryName][] = $collect;
            }
        }
    
        return $this->render('collect/list.html.twig', [
            'grouped_collects' => $grouped,
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
            return $this->redirectToRoute('app_collect_index');
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






#[Route('/templatefrontoffice/collect/pdf', name: 'collects_to_pdf')]
public function generatePdf(EntityManagerInterface $entityManager): Response
{
    // Fetch all Collect entities from the database
    $collects = $entityManager->getRepository(Collect::class)->findAll();

    // Render the Twig template as HTML
    $html = $this->renderView('templatefrontoffice/collect/pdf.html.twig', [
        'collects' => $collects,
    ]);

    // Initialize Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true); // Enable remote images if needed
    $dompdf = new Dompdf($options);

    // Load the HTML content
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to the browser
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'inline; filename="collects_list.pdf"');

    return $response;
}
 
    
}
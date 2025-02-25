<?php

namespace App\Controller\frontoffice;

use App\Entity\Commande;
use App\Entity\Annonce;
use App\Entity\User;
use App\Form\CommandeType;
use App\Form\CommandeType1;
use App\Form\CommandeType2;
use App\Repository\CommandeRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Mime\Address;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('templatefrontoffice/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/admin', name: 'app_commande_indexA', methods: ['GET'])]
public function indexA(CommandeRepository $commandeRepository, PaginatorInterface $paginator, Request $request): Response
{
    $query = $commandeRepository->createQueryBuilder('c')
        ->getQuery();

    $commandes = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        10 // Number of items per page
    );

    return $this->render('templatebackoffice/commande/indexA.html.twig', [
        'commandes' => $commandes,
    ]);
}

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AnnonceRepository $annonceRepository, SessionInterface $session, Security $security, MailerInterface $mailer): Response
    {   
      
        $user = $security->getUser();
    
        if (!$user) {
            
            return new RedirectResponse($this->generateUrl('app_login'));
        }
    
       
        $cart = $session->get('cart', []);
        if (empty($cart)) {
           
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_commande_index');
        }
        $annonceIds = array_keys($cart);
        $annonces = $annonceRepository->findBy(['id' => $annonceIds]);
    
      
        $totalAmount = 0;
        foreach ($annonces as $annonce) {
            $quantity = $cart[$annonce->getId()]; 
            $totalAmount += $annonce->getPrix() * $quantity;
        }
    
        
        $annonceQuantities = [];
    
        $commande = new Commande();
        
        $commande->setUser($user);
        $commande->setClientName($user->getUserIdentifier()); 
        $commande->setClientFamilyName(''); 
        $commande->setClientAdresse($user->getAdresse());
        $commande->setClientPhone($user->getTelephone());
        $commande->setEtatCommande("En Attente");
        $commande->setDate(new \DateTime());
        $commande->setPrixtotal($totalAmount); 
    
        foreach ($annonces as $annonce) {
            $quantity = $cart[$annonce->getId()]; 
            $commande->addAnnonce($annonce); 
            $annonce->addCommande($commande); 
           
            $annonceQuantities[$annonce->getId()] = $quantity;
            $annonce->setQuantite($annonce->getQuantite() - $annonceQuantities[$annonce->getId()]);
            if ($annonce->getQuantite() < 0) {
                $annonce->setQuantite(0);
            }
        }
    
        $commande->setAnnonceQuantities($annonceQuantities);
        $commande->setMethodePaiement($commande->getMethodePaiement());
    
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
          
            $entityManager->persist($commande);
            $entityManager->flush();
            $session->set('cart', []);
    
            $this->sendCommandeNotificationEmail($mailer, $user, $commande);
    
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }
    
    
        return $this->render('templatefrontoffice/commande/new.html.twig', [
            'form' => $form->createView(),
            'commandes' => $commande,
        ]);
    }
    
    private function sendCommandeNotificationEmail(MailerInterface $mailer, UserInterface $currentUser, Commande $commande): void
    {
        $email = (new TemplatedEmail())
            ->from("testp3253@gmail.com")
            ->to($currentUser->getEmail())
            ->subject('New commande Created')
            ->htmlTemplate('emails/factureL.html.twig')
            ->context([
                'currentUser' => $currentUser,
                'commande' => $commande,
            ]);
    
        $mailer->send($email);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('templatefrontoffice/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('admin/{id}', name: 'app_commande_showA', methods: ['GET'])]
    public function showA(Commande $commande): Response
    {
        return $this->render('templatebackoffice/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager, AnnonceRepository $annonceRepository): Response
    {
        $form = $this->createForm(CommandeType2::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $formData = $form->getData();
            $newTotalAmount = 0;
            foreach ($formData->getAnnonceQuantities() as $annonceId => $quantity) {
                $annonce = $annonceRepository->find($annonceId);
                if ($annonce) {
                    $newTotalAmount += $annonce->getPrix() * $quantity;
                }
            }
            
           
            $commande->setPrixtotal($newTotalAmount);
            
         
            $entityManager->flush();
    
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('templatefrontoffice/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'admin_commande_edit', methods: ['GET', 'POST'])]
    public function editA(Request $request, Commande $commande, EntityManagerInterface $entityManager, AnnonceRepository $annonceRepository): Response
    {
        $form = $this->createForm(CommandeType1::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $formData = $form->getData();
            $newTotalAmount = 0;
            foreach ($formData->getAnnonceQuantities() as $annonceId => $quantity) {
                $annonce = $annonceRepository->find($annonceId);
                if ($annonce) {
                    $newTotalAmount += $annonce->getPrix() * $quantity;
                }
            }
            
          
            $commande->setPrixtotal($newTotalAmount);
            
           
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_indexA', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('templatebackoffice/commande/editA.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_indexA', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/save', name: 'save-order', methods: ['POST'])]
    public function saveOrder(Request $request, SessionInterface $session, Security $security): Response
    {
        
        $user = $security->getUser();
        
        if (!$user) {
           
            return $this->redirectToRoute('app_login');
        }

        
        $orderDetails = $request->request->all();

       
        $orderDetails['client_name'] = $user->getUserIdentifier(); 
        $orderDetails['client_family_name'] = ''; 
        $orderDetails['user_id'] = $user->getId();
     
        $session->set('order_details', $orderDetails);

      
        return $this->redirectToRoute('checkout');
    }
}
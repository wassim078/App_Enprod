<?php

namespace App\Controller\frontoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Repository\AnnonceRepository;
use App\Entity\Annonce;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, Security $security): Response
    {
        return $this->render('templatefrontoffice/cart/index.html.twig', [
            'cart' => $session->get('panier')
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['GET'])]
    public function addToCart(int $id, Annonce $annonce, SessionInterface $session, EventDispatcherInterface $eventDispatcher, AnnonceRepository $annonceRepository, TranslatorInterface $translator): RedirectResponse
    {
        
        $cart = $session->get('cart', []);

       
        $annonceId = $annonce->getId();
        if (isset($cart[$annonceId])) {
           
            $cart[$annonceId]++;
        } else {
           
            $cart[$annonceId] = 1;
        }

        
        $session->set('cart', $cart);

        $annonce = $annonceRepository->find($id);
        $this->addFlash('success', sprintf('"%s" ajouté au panier.', $annonce->getTitre()));

        // Redirect back to the annonce page or any other desired route
        return $this->redirectToRoute('show_cart', ['id' => $annonce->getId()]);
    }

    #[Route('/show-cart', name: 'show_cart', methods: ['GET'])]
    public function showCart(SessionInterface $session, AnnonceRepository $annonceRepository): Response
    {
       
        $cart = $session->get('cart', []);

       
        $annonceIds = array_keys($cart);
        $annonces = $annonceRepository->findBy(['id' => $annonceIds]);

      
        $totalAmount = 0;
        foreach ($annonces as $annonce) {
            $quantity = $cart[$annonce->getId()];
            $totalAmount += $annonce->getPrix() * $quantity;
        }

        return $this->render('templatefrontoffice/cart/show.html.twig', [
            'annonces' => $annonces,
            'cart' => $cart,
            'totalAmount' => $totalAmount,
        ]);
    }

    #[Route('/clear-cart', name: 'clear_cart', methods: ['POST'])]
    public function clearCart(SessionInterface $session): JsonResponse
    {
       
        $session->remove('cart');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/update-cart/{id}', name: 'update_cart', methods: ['POST'])]
    public function updateCart(int $id, SessionInterface $session): RedirectResponse
    {
        $action = $_POST['action'] ?? null;

        if ($action === 'increase') {
            $this->changeQuantity($id, $session, 1);
        } elseif ($action === 'decrease') {
            $this->changeQuantity($id, $session, -1);
        }

        return $this->redirectToRoute('show_cart');
    }

    private function changeQuantity(int $id, SessionInterface $session, int $changeBy): void
    {
        
        $cart = $session->get('cart', []);

      
        if (array_key_exists($id, $cart)) {
        
            $cart[$id] += $changeBy;

            
            $cart[$id] = max(0, $cart[$id]);
        }

       
        $session->set('cart', $cart);
    }
}
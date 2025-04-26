<?php

namespace App\Controller\frontoffice;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CartService;
use App\Entity\Annonce;

#[Route('/cart', name: 'cart_')] // Updated prefix
class CartController extends AbstractController
{


    private $em;
    private $cartService;

    public function __construct(
        EntityManagerInterface $em,
        CartService $cartService
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
    }

    #[Route('/add/{id}', name: 'add', methods: ['POST'])]
    public function addToCart(int $id, Request $request): Response
    {
        // fetch the annonce so we can report its stock limit back
        $annonce = $this->em->getRepository(Annonce::class)->find($id);
        $max     = $annonce ? $annonce->getQuantite() : 0;

        try {
            $this->cartService->addItem($id);
            $type    = 'success';
            $message = 'Item added to cart';
        } catch (\RuntimeException $e) {
            $type    = 'error';
            $message = $e->getMessage();
        }

        // get the updated per-item count
        $cartItems = $this->cartService->getCart();
        $itemCount = $cartItems[$id] ?? 0;

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'type'      => $type,
                'message'   => $message,
                'count'     => $this->cartService->getTotalItems(),
                'itemCount' => $itemCount,
                'max'       => $max,
            ]);
        }

        $this->addFlash($type, $message);
        return $this->redirectToRoute('app_shop');
    }



    #[Route('/', name: 'view')]
    public function viewCart(): Response
    {
        $cartItems = $this->cartService->getCart();
        $annonceIds = array_keys($cartItems);

        // Get all annonces in one query
        $annonces = $this->em->getRepository(\App\Entity\Annonce::class)
            ->findBy(['id' => $annonceIds]);

        // Create an ID => Annonce map
        $annoncesMap = [];
        foreach ($annonces as $annonce) {
            $annoncesMap[$annonce->getId()] = $annonce;
        }

        return $this->render('templatefrontoffice/cart/index.html.twig', [
            'cart' => $cartItems,
            'annonces' => $annoncesMap,
            'total' => $this->cartService->getTotal()
        ]);
    }


    #[Route('/update/{id}/{action}', name: 'update', methods: ['POST'])]
    public function updateQuantity(int $id, string $action, CartService $cartService, Request $request): Response
    {
        try {
            $cartService->updateQuantity($id, $action);
            $message = 'Quantity updated';
            $messageType = 'success';
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            $messageType = 'error';
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => $messageType === 'success',
                'message' => $message,
                'count' => $cartService->getTotalItems(),
                'total' => $cartService->getTotal()
            ]);
        }

        $this->addFlash($messageType, $message);
        return $this->redirectToRoute('cart_view');
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['POST'])]
    public function removeItem(int $id, CartService $cartService, Request $request): Response
    {
        $cartService->removeItem($id);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'count' => $cartService->getTotalItems(),
                'total' => $cartService->getTotal()
            ]);
        }

        return $this->redirectToRoute('cart_view');
    }


    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clearCart(Request $request): Response
    {
        $this->cartService->clearCart();

        $this->addFlash('success', 'Cart cleared successfully');
        return $this->redirectToRoute('cart_view');
    }

}
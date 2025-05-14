<?php

namespace App\Controller\frontoffice;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/templatefrontoffice/annonce', name: 'app_shop')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        // 1) fetch all annonces (with category)
        $annonces = $annonceRepository->findAllWithCategory();

        // 2) get the raw [ annonceId => qty ] array from the service
        $cart = $this->cartService->getCart();

        // 3) pass both annonces and cart into Twig
        return $this->render('templatefrontoffice/annonce/index.html.twig', [
            'annonces' => $annonces,
            'cart'     => $cart,
        ]);
    }
}

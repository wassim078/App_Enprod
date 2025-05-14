<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\CartService;

class CartExtension extends AbstractExtension
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_count', [$this, 'getTotalItems']), // Match template usage
        ];
    }

    public function getTotalItems(): int
    {
        return $this->cartService->getTotalItems();
    }
}
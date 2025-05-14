<?php

namespace App\Service;

use App\Entity\Panier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use App\Entity\Annonce;

class CartService
{
    private $requestStack;
    private $security;
    private $em;

    public function __construct(
        RequestStack $requestStack,
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->em = $em;
    }

    public function getCart(): array
    {
        $user = $this->security->getUser();

        if ($user) {
            $panier = $this->em->getRepository(Panier::class)->findOneBy(['user' => $user]);
            return $panier ? $panier->getItems() : [];
        }

        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function addItem(int $annonceId): void
    {
        $annonce = $this->em->getRepository(Annonce::class)->find($annonceId);
        if (!$annonce) {
            throw new \InvalidArgumentException('Product not found');
        }

        $items = $this->getCart();
        $currentQty = $items[$annonceId] ?? 0;

        // Prevent adding if current quantity >= available stock
        if ($currentQty >= $annonce->getQuantite()) {
            throw new \RuntimeException(
                sprintf('Maximum quantity (%d) reached for %s',
                    $annonce->getQuantite(),
                    $annonce->getTitre())
            );
        }

        $items[$annonceId] = $currentQty + 1;
        $this->saveCart($items);
    }

    private function saveDatabaseCart(User $user, array $items): void
    {
        $panier = $this->em->getRepository(Panier::class)->findOneBy(['user' => $user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $panier->setCreatedAt(new \DateTime());
        }

        $panier->setItems($items);
        $panier->setUpdatedAt(new \DateTime());

        $this->em->persist($panier);
        $this->em->flush();
    }

    private function saveSessionCart(array $items): void
    {
        $this->requestStack->getSession()->set('cart', $items);
    }


    public function getTotalItems(): int
    {
        $items = $this->getCart();
        return array_sum($items);
    }


    // Add these methods to your CartService
    public function updateQuantity(int $annonceId, string $action): void
    {
        $annonce = $this->em->getRepository(Annonce::class)->find($annonceId);
        if (!$annonce) {
            throw new \InvalidArgumentException('Product not found');
        }

        $items = $this->getCart();
        if (!isset($items[$annonceId])) return;

        if ($action === 'increase') {
            if ($items[$annonceId] >= $annonce->getQuantite()) {
                throw new \RuntimeException('Maximum quantity reached for this item');
            }
            $items[$annonceId]++;
        } elseif ($action === 'decrease') {
            $items[$annonceId] = max(1, $items[$annonceId] - 1);
        }

        $this->saveCart($items);
    }

    public function removeItem(int $annonceId): void
    {
        $items = $this->getCart();
        unset($items[$annonceId]);
        $this->saveCart($items);
    }

    public function getTotal(): float
    {
        $total = 0;
        $annonceIds = array_keys($this->getCart());

        $annonces = $this->em->getRepository(Annonce::class)
            ->findBy(['id' => $annonceIds]);

        foreach ($this->getCart() as $id => $quantity) {
            foreach ($annonces as $annonce) {
                if ($annonce->getId() == $id) {
                    $total += $annonce->getPrix() * $quantity;
                    break;
                }
            }
        }

        return $total;
    }

    private function saveCart(array $items): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $this->saveDatabaseCart($user, $items);
        } else {
            $this->saveSessionCart($items);
        }
    }

    public function clearCart(): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $panier = $this->em->getRepository(Panier::class)->findOneBy(['user' => $user]);
            if ($panier) {
                $panier->setItems([]);
                $panier->setUpdatedAt(new \DateTime());
                $this->em->flush();
            }
        } else {
            $this->requestStack->getSession()->set('cart', []);
        }
    }


}
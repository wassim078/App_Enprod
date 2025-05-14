<?php

namespace App\Controller\frontoffice;
use App\Entity\Annonce;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/commande', name: 'commande_')]
class   CommandeController extends AbstractController
{
    private $em;
    private $cartService;
    private $security;

    public function __construct(
        EntityManagerInterface $em,
        CartService $cartService,
        Security $security
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->security = $security;
    }


    #[Route('/checkout', name: 'checkout')]
    public function checkout(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get cart items
            $cartItems = $this->cartService->getCart();

            // Prepare annonce quantities for JSON storage
            $annonceQuantities = [];
            foreach ($cartItems as $annonceId => $quantity) {
                $annonceQuantities[$annonceId] = $quantity; // Store as key-value pair
            }

            // Set commande data
            $commande->setUser($user);
            $commande->setDate(new \DateTime());
            $commande->setAnnonceQuantities($annonceQuantities);
            $commande->setPrixtotal($this->cartService->getTotal()); // Assuming total in millimes

            // Set status based on payment method
            if ($commande->getMethodePaiement() === 'E-paiement') {
                $commande->setEtatCommande('PAID');
            } else {
                $commande->setEtatCommande('AWAITING_DELIVERY');
            }

            // Persist and flush
            $this->em->persist($commande);
            $this->em->flush();

            // Clear the cart
            $this->cartService->clearCart();

            if ($commande->getMethodePaiement() === 'E-paiement') {
                // Configure Stripe
                \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                // Create line items for Stripe
                $lineItems = [];
                foreach ($cartItems as $annonceId => $quantity) {
                    $annonce = $this->em->getRepository(Annonce::class)->find($annonceId);
                    if ($annonce) {
                        $lineItems[] = [
                            'price_data' => [
                                'currency' => 'usd',
                                'unit_amount' => (int)($annonce->getPrix()), // Adjust if needed
                                'product_data' => [
                                    'name' => $annonce->getTitre(),
                                ],
                            ],
                            'quantity' => $quantity,
                        ];
                    }
                }

                // Create Stripe Checkout session
                $checkoutSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $this->generateUrl('commande_success', [
                        'id' => $commande->getId(),
                        'session_id' => '{CHECKOUT_SESSION_ID}' // Add this line
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->generateUrl('commande_cancel', ['id' => $commande->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'metadata' => [
                        'commande_id' => $commande->getId(),
                    ],
                ]);

                return $this->redirect($checkoutSession->url);
            }

            $this->addFlash('success', 'Commande passée avec succès!');
            return $this->redirectToRoute('commande_confirmation', ['id' => $commande->getId()]);
        }

        return $this->render('templatefrontoffice/commande/checkout.html.twig', [
            'form' => $form->createView(),
            'total' => $this->cartService->getTotal(), // Total in millimes
        ]);
    }

    #[Route('/success/{id}', name: 'success')]
    public function success(Request $request, Commande $commande): Response
    {
        // Verify user access
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this commande.');
        }

        // Check commande state
        if ($commande->getEtatCommande() !== 'PAID') {
            throw $this->createAccessDeniedException('Invalid commande state.');
        }

        // Get session ID from request parameters
        $sessionId = $request->query->get('session_id');
        if (!$sessionId) {
            throw $this->createNotFoundException('Session ID not provided.');
        }

        // Configure Stripe
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // Verify payment status
            if ($session->payment_status === 'paid' && $session->metadata->commande_id == $commande->getId()) {
                $commande->setEtatCommande('PAID');
                $this->em->flush();
                $this->addFlash('success', 'Paiement réussi! Votre commande est confirmée.');
            } else {
                $this->addFlash('error', 'Erreur lors de la vérification du paiement.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur de traitement du paiement: '.$e->getMessage());
        }

        return $this->redirectToRoute('commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this commande.');
        }

        $this->addFlash('warning', 'Le paiement a été annulé. Votre commande est en attente de paiement.');
        return $this->redirectToRoute('home'); // Adjust as needed
    }

    #[Route('/confirmation/{id}', name: 'confirmation')]
    public function confirmation(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this commande.');
        }

        return $this->render('templatefrontoffice/commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }


    #[Route('/mes-commandes', name: 'list')]
    public function listCommandes(Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $commandes = $this->em->getRepository(Commande::class)->findBy(
            ['user' => $user],
            ['date' => 'DESC']
        );

        // Process annonce quantities
        $annoncesDetails = [];
        foreach ($commandes as $commande) {
            $quantities = $commande->getAnnonceQuantities() ?? [];
            $details = [];
            foreach ($quantities as $annonceId => $quantity) {
                $annonce = $this->em->getRepository(Annonce::class)->find($annonceId);
                if ($annonce) {
                    $details[] = [
                        'title' => $annonce->getTitre(),
                        'quantity' => $quantity
                    ];
                }
            }
            $annoncesDetails[$commande->getId()] = $details;
        }

        return $this->render('templatefrontoffice/commande/list.html.twig', [
            'commandes' => $commandes,
            'annoncesDetails' => $annoncesDetails
        ]);
    }
}
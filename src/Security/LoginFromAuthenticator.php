<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;


class LoginFromAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private JWTEncoderInterface $jwtEncoder;
    public function __construct(private UrlGeneratorInterface $urlGenerator,JWTEncoderInterface $jwtEncoder)
    {
        $this->urlGenerator = $urlGenerator;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        $roles = $user->getRoles();
        // Redirect based on the user's role
        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_templatebackoffice'));
        } elseif (in_array('ROLE_CLIENT', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_templatefrontoffice'));
        } elseif (in_array('ROLE_LIVREUR', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_templatefrontoffice'));
        } elseif (in_array('ROLE_ENTREPRISE', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_templatefrontoffice'));
        }

        // Fallback route if no role matches
        return new RedirectResponse($this->urlGenerator->generate('app_templatefrontoffice'));


        /*
        // Get the user's roles
        

        $jwtToken = $this->jwtEncoder->encode([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
        $response = $this->createRedirectResponse($user);
        $response->headers->setCookie(
            new \Symfony\Component\HttpFoundation\Cookie(
                "jwt_token", // Cookie name
                $jwtToken,   // JWT token value
                time() + 3600, // Expiration time (1 hour)
                '/',        // Path
                null,       // Domain
                false,      // Secure (set to true if using HTTPS)
                true        // HttpOnly
            )
        );
        return $response;
        */

    }
    
    

        // For example:
        // return new RedirectResponse($this->urlGenerator->generate('some_route'));
       
    

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

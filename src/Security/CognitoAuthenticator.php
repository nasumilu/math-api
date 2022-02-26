<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class CognitoAuthenticator extends AbstractAuthenticator
{

    public function __construct(private CognitoUserProvider $userProvider)
    {
        
    }

    public function supports(Request $request): ?bool
    {
        return false; // disable this Authenticator
        //return 0 === strpos($request->headers->get('Authorization', ''), 'Bearer');
    }

    public function authenticate(Request $request): Passport
    {
        $token = trim(substr($request->headers->get('Authorization', ''), 6));
        return new SelfValidatingPassport(new UserBadge($token, [$this->userProvider, 'loadUserByIdentifier']));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;  
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }


}

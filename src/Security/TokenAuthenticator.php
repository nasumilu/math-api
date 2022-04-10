<?php

namespace App\Security;

use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserRepository $userRepository, private TokenRepository $tokenRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && str_starts_with($request->headers->get('Authorization'), 'Bearer');
    }

    public function authenticate(Request $request): Passport
    {
        [$payload, $token] = explode('.', substr($request->headers->get('Authorization'), 7));
        return new Passport(
            new UserBadge($payload, function(string $payload): UserInterface {
                $value = json_decode(base64_decode($payload), true);
                return $this->userRepository->findOneBy(['id' => $value['user_id'], 'email' => $value['email']]);
            }),
            new CustomCredentials(
                fn(string $credentials, UserInterface $user) : bool => $this->tokenRepository->findNonExpiredTokenForUser($user)->getId() === $credentials,
                $token
            )
        );
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

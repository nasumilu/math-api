<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Token;
use App\Repository\ClientRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private TokenRepository $tokenRepository,
        private ClientRepository $clientRepository,
        private UserRepository $userRepository)
    {
    }

    #[Route('/oauth/token',
        name: 'app_token',
        methods: ['POST'],
        condition: "request.request.has('grant_type') and request.request.get('grant_type') in ['password', 'client_credentials', 'code', 'refresh_token']"
    )]
    public function token(Request $request): Response
    {

        $data = [];
        $status = 200;
        try {
            if (null === $client = $this->clientRepository->findOneBy([
                    'id' => $request->request->get('client_id'),
                    'secret' => $request->request->get('client_secret')
                ])) {
                throw new UnauthorizedHttpException('Basic', 'Unauthorized');
            }

            if ('password' === $request->request->get('grant_type')) {
                $data = $this->generateTokenForPasswordGrantType($request);
            }
        } catch(HttpException $ex) {
            $status = $ex->getStatusCode();
            $data['message'] = $ex->getMessage();
        }

        return $this->json(data: $data, status: $status);

    }

    private function generateTokenForPasswordGrantType(Request $request): array
    {
        if(null === $user = $this->userRepository->findOneBy(['email' => $request->request->get('username')])) {
            throw new UnauthorizedHttpException('OAuth2', 'Unauthorized');
        }


        if(!$this->passwordHasher->isPasswordValid($user, $request->request->get('password'))) {
            throw new UnauthorizedHttpException('OAuth2', 'Unauthorized');
        }

        $token = $this->tokenRepository->findOrCreateTokenFor($user);

        $payload = base64_encode(json_encode([
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]));

        return [
            'token_type' => 'Bearer',
            'expires_at' => $token->getExpiresAt(),
            'access_token' => $payload. '.' . $token->getId()
        ];

    }

}

<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    public function __construct(private UserRepository $repository, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $plainPwd = $form->get('password')->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPwd));
            $this->repository->add($user);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Service\PhotoUploader;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response,Request};
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    public function __construct(private ProfileRepository $repository, private PhotoUploader $uploader)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/profile.{_format}',
        name: 'app_profile',
        requirements: [
            '_format' => 'html|json'
        ],
        format: 'html'
    )]
    public function index(Request $request, ?string $_format = null): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        if('json' === $_format) {

            return $this->json([
                'name' => $profile->getName(),
                'email' => $user->getUserIdentifier(),
                'photo' => $this->uploader->getUrl($profile)
            ]);
        }

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if($photoFile) {
                // This is not safe and needs to be placed in an update event listener
                $this->uploader->unlinkOldPhoto($profile);
                $profile->setPhoto($this->uploader->upload($photoFile));
            }
            $this->repository->add($profile);
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }
}

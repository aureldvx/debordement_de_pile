<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\SignupType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(
        path: '/signup',
        name: 'auth_signup',
        methods: ['GET', 'POST']
    )]
    public function signup(Request $request): Response
    {
        $user = new User();

        $form = $this
            ->createForm(SignupType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setSlug($user->getPseudo())
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    strval($form->get('password')->getData())
                ));

            $this->userRepository->add($user, true);

            return $this->redirectToRoute('auth_login');
        }

        return $this->renderForm('security/signup.html.twig', [
            'signup_form' => $form,
        ]);
    }
}

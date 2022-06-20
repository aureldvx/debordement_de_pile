<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\EditPasswordType;
use App\Form\User\EditPersonalDataType;
use App\Form\User\EditPseudoType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/u', name: 'user_')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/', name: 'edit_profile', methods: ['GET', 'PATCH'])]
    public function editProfile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $pseudoForm = $this->handlePseudoForm($request, $user);
        $passwordForm = $this->handlePasswordForm($request, $user);
        $personalDataForm = $this->handlePersonalDataForm($request, $user);

        return $this->renderForm('user/edit-profile.html.twig', [
            'pseudo_form' => $pseudoForm,
            'password_form' => $passwordForm,
            'personal_data_form' => $personalDataForm,
        ]);
    }

    #[Route(path: '/{slug}', name: 'profile_by_slug', methods: ['GET'])]
    public function viewProfile(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/{slug}', name: 'close_account', methods: ['DELETE'])]
    public function closeProfile(User $user): Response
    {
        if ($this->isGranted('CLOSE_ACCOUNT', $user)) {
            $user->setEnabled(false);
            $this->userRepository->add($user, true);

            return $this->redirectToRoute('auth_logout');
        }

        $this->addFlash('error', 'Vous n\'avez pas les droits pour supprimer ce profil.');

        return $this->redirectToRoute('user_edit_profile', ['slug' => $user->getSlug()]);
    }

    private function handlePseudoForm(Request $request, User $user): FormInterface
    {
        $updatePseudoForm = $this
            ->createForm(EditPseudoType::class, $user)
            ->handleRequest($request);

        if ($updatePseudoForm->isSubmitted() && $updatePseudoForm->isValid()) {
            $plainPassword = strval($updatePseudoForm->get('password')->getData());
            $passwordValid = $this->passwordHasher->isPasswordValid($user, $plainPassword);

            if ($passwordValid) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            }

            $this->userRepository->add($user, true);
            $this->addFlash('success', 'Votre pseudo a bien été mis à jour !');
        }

        return $updatePseudoForm;
    }

    private function handlePasswordForm(Request $request, User $user): FormInterface
    {
        $updatePasswordForm = $this
            ->createForm(EditPasswordType::class, $user)
            ->handleRequest($request);

        if ($updatePasswordForm->isSubmitted() && $updatePasswordForm->isValid()) {
            $plainOldPassword = strval($updatePasswordForm->get('password')->getData());
            $plainNewPassword = strval($updatePasswordForm->get('newPassword')->getData());
            $passwordValid = $this->passwordHasher->isPasswordValid($user, $plainOldPassword);

            if ($passwordValid) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainNewPassword));
            }

            $this->userRepository->add($user, true);
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour !');
        }

        return $updatePasswordForm;
    }

    private function handlePersonalDataForm(Request $request, User $user): FormInterface
    {
        $updatePersonalDataForm = $this
            ->createForm(EditPersonalDataType::class, $user)
            ->handleRequest($request);

        if ($updatePersonalDataForm->isSubmitted() && $updatePersonalDataForm->isValid()) {
            $this->userRepository->add($user, true);
            $this->addFlash('success', 'Vos informations personnelles ont bien été mises à jour !');
        }

        return $updatePersonalDataForm;
    }
}

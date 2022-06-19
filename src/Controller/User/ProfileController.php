<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\Security\EditPersonalDataType;
use App\Form\User\EditPasswordType;
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
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(path: '/', name: 'edit_profile')]
    public function editProfile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $pseudoForm = $this->handlePseudoForm($request, $user);
        $passwordForm = $this->handlePasswordForm($request, $user);
        $personalDataForm = $this->handlePersonalDataForm($request, $user);

        return $this->renderForm('user/profile.html.twig', [
            'pseudo_form' => $pseudoForm,
            'password_form' => $passwordForm,
            'personal_data_form' => $personalDataForm,
        ]);
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

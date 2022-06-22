<?php

namespace App\Security;

use App\Entity\LoginActivity;
use App\Entity\User;
use App\Helper\DateTimeHelpers;
use App\Helper\UserHelpers;
use App\Repository\LoginActivityRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly LoginActivityRepository $loginActivityRepository,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = strval($request->get('_username'));
        $plainPassword = strval($request->get('_password'));
        $csrfToken = strval($request->get('_csrf_token'));

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username, function ($userIdentifier) {
                return $this->userRepository->findOneBy([
                    'pseudo' => $userIdentifier,
                    'enabled' => true,
                    'blockedAt' => null,
                ]);
            }),
            new PasswordCredentials($plainPassword),
            [new CsrfTokenBadge('authenticate', $csrfToken)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User|null $user */
        $user = $token->getUser();

        if ($user) {
            $loginActivity = new LoginActivity();
            $loginActivity
                ->setRelatedUser($user)
                ->setIpAddress(UserHelpers::getIp())
                ->setConnectedAt(DateTimeHelpers::createImmutable());
            $this->loginActivityRepository->add($loginActivity, true);
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('public_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('auth_login');
    }
}

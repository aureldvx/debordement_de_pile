<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

trait RequestContentValidatorTrait
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * @param string[]|null $authorizedSubjects
     * @param string[]|null $authorizedTypes
     */
    private function validateSubmittedData(
        Request $request,
        ?array $authorizedSubjects = null,
        ?array $authorizedTypes = null,
    ): bool {
        if (!$this->security->isGranted('ROLE_USER')) {
            $request->getSession()->getFlashBag()->add('warning', 'Vous devez être connecté pour ajouter une réaction ou signaler un contenu.');

            return false;
        }

        $subject = strval($request->get('subject'));
        $type = strval($request->get('type'));

        if ($authorizedSubjects && !in_array($subject, $authorizedSubjects)) {
            return false;
        }

        if ($authorizedTypes && !in_array($type, $authorizedTypes)) {
            return false;
        }

        return true;
    }
}

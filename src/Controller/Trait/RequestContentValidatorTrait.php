<?php

namespace App\Controller\Trait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

trait RequestContentValidatorTrait
{
    /**
     * @param string[]|null $authorizedSubjects
     * @param string[]|null $authorizedTypes
     */
    private function validateSubmittedData(
        Request $request,
        Security $security,
        string $grantedAttribute,
        mixed $grantedSubject = null,
        ?array $authorizedSubjects = null,
        ?array $authorizedTypes = null,
    ): bool {
        if (!$security->isGranted($grantedAttribute, $grantedSubject)) {
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

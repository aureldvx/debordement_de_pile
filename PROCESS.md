# Process de développement

1. Initialisation du projet
   1. First commit de Symfony
   2. Installation des outils de qualité de code
      - phpcpd
      - phpcs
      - phpcsfixer
      - phpmd
      - phpstan
   3. Création du `docker-compose.yml` pour la BDD postgres
   4. Installation des dépendances Symfony
      - doctrine/orm
      - sensio/framework-extra-bundle
      - symfony/security
      - symfony/debug-bundle
      - doctrine/doctrine-fixtures-bundle
      - symfony/maker-bundle
      - fakerphp/faker
      - symfony/uid
      - symfony/test-pack
   5. Création de l'entité User
   6. Création de l'entité Category et de sa relation à User
   7. Création de l'entité Ticket et de ses relations
   8. Création de l'entité Comment et de ses relations
   9. Création de l'entité Vote et de ses relations
   10. Création de l'entité Report et de ses relations
   11. Installation du package symfony/validator
   12. Création de l'entité LoginActivity et de sa relation à User
   13. Création de fichiers dans le build pour pouvoir lancer PHPStan
   14. Refacto des entités en créant des traits pour les attributs en commun
   15. Création des fixtures
       - AbstractFixture qui implémente des méthodes pour faciliter la manipulation des références
       - UserFixtures
       - CategoryFixtures
       - TicketFixtures
       - CommentFixtures
       - ReportFixtures
       - VoteFixtures
       - LoginActivityFixtures
   16. Installation des dépendances front
       - webpack-encore
       - bootstrap
       - bootstrap-icons
   17. Création des différents templates twig à étendre
   18. Installation des packages symfony/form et symfony/csrf
   19. Définition de bootstrap en thème de formulaire par défaut
   20. Processus d'authentification
   21. Processus de création de compte
   22. Édition du profil
   23. Installation du package twig/string-extra
   24. Vue du profil utilisateur
   25. Clôture de compte par l'utilisateur
   26. Ajout de la possibilité de clore un ticket
   27. CRUD des catégories côté ROLE_USER
   28. Ajout de la pagination dans les pages de listing
   29. CUD des tickets côté ROLE_USER
   30. Modification du thème de formulaire pour afficher les messages d'erreur en raw
   31. Page single d'un ticket avec affichage des commentaires
   32. Ajout des réactions sur tickets/commentaires
   33. Empêcher la connexion aux utilisateurs bloqués ou clôturés
   34. Anonymiser les références aux pseudos des utilisateurs clôturés/bloqués
   35. Création de la page d'accueil
   36. Création du dashboard admin
   37. ADMIN
       1. Listing de l'activité de connexion
       2. Création d'une LoginActivity via le authenticationSuccess
   38. FRONT : Bloquer la création de signalement à 1 par user et ticket/commentaire
   39. ADMIN
       1. CRUD des signalements
       2. CRUD des catégories
       3. CRUD des utilisateurs
       4. Suppression de tickets/commentaires
       5. Switch User
       6. Listing des tickets
   40. Création du README

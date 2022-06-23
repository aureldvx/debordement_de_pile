# Débordement de code

## Installation

### Pré-requis

Pour installer ce projet, vous aurez besoin des outils suivants installés globalement sur votre machine :
- [PHP 8.1+](https://www.php.net/releases/8.1/en.php)
- [Composer 2.1+](https://getcomposer.org/)
- [Symfony CLI 5.4+](https://symfony.com/download)
- [Docker](https://docs.docker.com/get-docker/)
- [Node 16.15+](https://nodejs.org/en/)

### Clone du repository

Une fois les pré-requis correctement installés, vous pouvez clone le repo sur votre machine :

```sh
cd /emplacement/de/destination
git clone https://github.com/aureldvx/debordement_de_pile.git
```

### Installation des dépendances

```sh
cd /emplacement/du/projet
# Installation des dépendances Symfony
composer install
# Installation des dépendances front
npm install
```

À cette étape, l'ensemble des dépendances nécessaires au projet sont installées, il faut maintenant configurer l'environnement.

## Configuration

La première étape est de dupliquer le fichier `.env` à la racine du projet en le renommer en `.env.local`. Il ne sera ainsi pas ajouté dans le repository git, pour protéger vos informations sensibles.

### `docker-compose.yml`

Une fois le fichier créé, vérifiez les informations spécifiées dans le fichier `docker-compose.yml` :
```yaml
version: '3.1'

services:
  postgres_db:
    image: postgres:13
    restart: always
    environment:
      POSTGRES_USER: myuser # modifiez si vous le souhaitez
      POSTGRES_PASSWORD: mypassword # modifiez si vous le souhaitez
      POSTGRES_DB: debordement_pile # modifiez si vous le souhaitez
    ports:
      - "65432:5432" # modifiez si vous le souhaitez, mais gardez la partie ":5432"
    volumes:
      - postgres_db_vol:/var/lib/postgresql/data:rw

volumes:
  postgres_db_vol:

```

### `.env.local`

Si vous êtes satisfait de votre configuration, vous pouvez ensuite passer à la modification du `.env.local` :
```toml
# ...

# DATABASE_URL
# Modifier les parties POSTGRES_USER, POSTGRES_PASSWORD, POSTGRES_PORT, POSTGRES_DB
# par les valeurs entrées dans le fichier docker-compose.yml
DATABASE_URL="postgresql://POSTGRES_USER:POSTGRES_PASSWORD@127.0.0.1:POSTGRES_PORT/POSTGRES_DB?serverVersion=13&charset=utf8"
```

## Initialisation des données

Maintenant que la configuration est terminée, vous allez pouvoir initialiser les données de test.

Au préalable, vous devez lancer le conteneur Docker avec la commande suivante :
```sh
docker compose up -d
```

### Chargement des migrations

```sh
symfony console doctrine:migrations:migrate
# Appuyez sur Entrée pour valider le process
```

### Chargement des fixtures

```sh
symfony console doctrine:fixtures:load
# Tapez "yes" et appuyez sur Entrée pour valider le process
```

Vous devriez désormais avoir rempli votre base de données avec des données de test.

## Lancement de l'application

Si vous avez réalisé toutes les étapes précédentes, vous pouvez désormais lancer l'application :

```sh
# Lancer le serveur de développement
symfony server:start

# Compiler les assets
npm run build
```

Vous pouvez alors accéder au site avec l'URL affichée dans votre terminal.

## Comptes utilisateurs de test

Deux comptes utilisateurs sont créés spécialement pour voir les différentes fonctionnalités de l'application :

- Compte utilisateur classique
    - Identifiant : `user`
    - Mot de passe : `password`
- Compte administrateur
    - Identifiant : `admin`
    - Mot de passe : `password`

## Commandes utiles

- Si vous avez besoin de régénérer des données de test, vous pouvez ré-exécuter les fixtures avec la commande suivante :
    ```sh
    symfony console doctrine:fixtures:load --purge-with-truncate
    ```
- Lorsque vous avez fini de travailler sur l'application, pensez à arrêter le conteneur Docker pour économiser des ressources sur votre machine :
    ```sh
    docker compose down
    ```

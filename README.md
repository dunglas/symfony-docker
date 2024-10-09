# Description de l'application

_Ce projet est en cours de développement, certaines fonctionnalités ne sont pas encore présentes et ce fichier sera très probablement ammener à être modifier_

App Accident est une application Symfony destinée à gérer les accidents des bus de la RRTHV. Cela comprend de nombreux détails avec plusieurs entités principales : user, bus, line (ligne de bus), incident, photo, incidentPhoto.

Cette application permettra pour les conducteurs de bus, à enregistrer un accident via un formulaire.

Pour les personnes administratives, cette App permettra d'analyser les données récoltées dans la BDD pour pouvoir les traiter.

## Fonctionnalités principales

- **Gestion des incidents de bus** : Permet la création, la mise à jour et la suppression d'incidents, garantissant une traçabilité complète des événements.
- **Association d'incidents avec des photos** : Les conducteurs peuvent ajouter des images aux rapports d'incidents pour un contexte visuel.
- **Gestion des entités principales** : Suivi des utilisateurs, des bus, des lignes de bus et des incidents.
- **Interface d'administration via EasyAdmin** : Un tableau de bord intuitif pour gérer facilement les données de l'application.
- **Rapports analytiques** : Génération de rapports basés sur les incidents enregistrés, permettant une prise de décision informée.

## Technologies utilisées

1. **[Docker](https://www.docker.com/)** : Pour la conteneurisation de l'application, facilitant le déploiement et la gestion des environnements.
2. **[Symfony](https://symfony.com/)** : Framework PHP robuste pour le développement d'applications web.
3. **[MySQL](https://www.mysql.com/fr/)** : Base de données relationnelle utilisée pour stocker les informations de l'application.
4. **[phpMyAdmin](https://www.phpmyadmin.net/)** : Outil de gestion de base de données via une interface web pour simplifier l'administration de MySQL.
5. **[Twig](https://twig.symfony.com/)** : Moteur de templates utilisé pour la gestion des vues dans Symfony.
6. **[Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/index.html)** : Outil de mappage objet-relationnel pour interagir avec la base de données.
7. **[ApiPlatform](https://api-platform.com/)** : qui permet de gérer l'API. 

## Prérequis

Avant de démarrer, assurez-vous d'avoir installé les outils suivants :

1. Si ce n'est pas déjà fait, [installez Docker Compose](https://docs.docker.com/compose/install/) (v2.10+).
2. Clonez ce projet
3. Exécutez `docker compose build --no-cache` pour construire des images fraîches.
4. Exécutez `docker compose up --pull always -d --wait` pour configurer et démarrer un nouveau projet Symfony.
5. Ouvrez `https://localhost` dans votre navigateur web préféré et [acceptez le certificat TLS auto-généré](https://stackoverflow.com/a/15076602/1352334).
6. Exécutez `docker compose down --remove-orphans` pour arrêter les conteneurs Docker.

## Documentation

L'application est composée de 6 entités dont une entité (table) de relation (IncidentPhoto.php).

1. Pour la [gestion des entités](docs/easyadmin.md) incluant les CRUD j'utilise EasyAdmin
2. Pour la gestion de l'Api j'utilise l'outil ApiPlatform

## Licence

Symfony Docker est disponible sous la licence MIT.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).












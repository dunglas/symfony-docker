# Description de l'application

App Accident est une application Symfony destinée à gérer les accidents des bus de la RRTHV. Cela comprend de nombreux détails avec plusieurs entités principales : user, bus, line (ligne de bus), incident, photo, incidentPhoto.

Cette application permettra pour les conducteurs de bus, à enregistrer un accident via un formulaire.

Pour les personnes administratives, cette App permettra d'analyser les données récoltées dans la BDD pour pouvoir les traiter.

## Fonctionnalités principales

- Gestion des incidents de bus (création, mise à jour, suppression).
- Association d'incidents avec des photos.
- Gestion des entités principales : utilisateurs, bus, lignes de bus, incidents.
- Interface d'administration via **EasyAdmin** pour gérer facilement les données.

## Technologies utilisées

Ce projet utilise : 
1. [Docker](https://www.docker.com/)
2. Le Framework [Symfony](https://symfony.com/)
3. Une base de données [MySQL](https://www.mysql.com/fr/) avec [phpMyAdmin](https://www.phpmyadmin.net/) pour la gestion de celle-ci.

## Prérequis

1. Si ce n'est pas déjà fait, [installez Docker Compose](https://docs.docker.com/compose/install/) (v2.10+).
2. Clonez ce projet
3. Exécutez `docker compose build --no-cache` pour construire des images fraîches.
4. Exécutez `docker compose up --pull always -d --wait` pour configurer et démarrer un nouveau projet Symfony.
5. Ouvrez `https://localhost` dans votre navigateur web préféré et [acceptez le certificat TLS auto-généré](https://stackoverflow.com/a/15076602/1352334).
6. Exécutez `docker compose down --remove-orphans` pour arrêter les conteneurs Docker.

## Documentation

1. [Easy Admin](docs/easyadmin.md)

## Licence

Symfony Docker is disponible sous la licenc MIT.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).







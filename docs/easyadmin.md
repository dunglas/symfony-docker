## EasyAdmin

L'application **App Accident** utilise **EasyAdmin** pour gérer l'interface d'administration de manière intuitive. EasyAdmin permet de créer, modifier, et supprimer des entités grâce à une interface utilisateur simplifiée.

Dans cette documentation, nous allons voir comment les entités principales telles que les utilisateurs, les bus, les lignes de bus, et les incidents sont gérées via EasyAdmin.

## Installation et Configuration

EasyAdmin est déjà installé dans le projet via Composer. Si vous avez besoin de l'installer ou de le mettre à jour, utilisez les commandes suivantes :

```bash
composer require easycorp/easyadmin-bundle 
```

Dans le config/bundles.php :

````bash
return [
    // ...
    EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle::class => ['all' => true],
];
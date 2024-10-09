# EasyAdmin

- [Documentation officielle d'EasyAdmin](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)

L'application **App Accident** utilise **EasyAdmin** pour gérer l'interface d'administration de manière intuitive. EasyAdmin permet de créer, modifier, et supprimer des entités grâce à une interface utilisateur simplifiée.

Dans cette documentation, nous allons voir comment les entités principales telles que les utilisateurs, les bus, les lignes de bus, et les incidents sont gérées via EasyAdmin.

## Installation 

EasyAdmin est déjà installé dans le projet via Composer. Si vous avez besoin de l'installer ou de le mettre à jour, utilisez les commandes suivantes :

```bash
composer require easycorp/easyadmin-bundle 
```

Dans le 'config/bundles.php' :

````php
return [
    // ...
    EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle::class => ['all' => true],
];
````

## Exemple de gestion avec l'entité Incident (en relation avec plusieurs tables)

````bash
php bin/console make:admin:dashboard

php bin/console make:admin:crud
````

Modifier les fonctions dans 'IncidentCrudController.php'

````php
    public function configureCrud(Crud $crud): Crud
    {
        ...
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ...
            AssociationField::new('user', 'Users'), //qui permet de récupérer les enregistrements d'une autre table dans le formulaire de création des incidents, dans ce cas, on récupère la table 'user'
        ];
    }
    ````


















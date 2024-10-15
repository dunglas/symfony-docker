<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;


class PhotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new( 'description'),
            AssociationField::new('add_by', 'Users'),
            ImageField::new('file')
            ->setBasePath('uploads/photos')  // Chemin accessible via l'URL
            ->setUploadDir('public/uploads/photos')  // Chemin physique sur le serveur 
            
        ];
    }
    
}

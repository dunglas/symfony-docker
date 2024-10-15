<?php

namespace App\Controller\Admin;

use App\Entity\IncidentPhoto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; 
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;


class IncidentPhotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IncidentPhoto::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('IncidentPhoto')
            ->setEntityLabelInPlural('IncidentPhotos');
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            //TextField::new('title', 'Titre'),
            AssociationField::new('incident', 'Incidents'),
            AssociationField::new('photo', 'Photo')
        ];
    }
}

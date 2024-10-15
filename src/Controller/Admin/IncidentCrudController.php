<?php

namespace App\Controller\Admin;

use App\Entity\Incident;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; 
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;


class IncidentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Incident::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Incident')
            ->setEntityLabelInPlural('Incidents');
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            //TextField::new('title', 'Titre'),
            AssociationField::new('bus', 'Bus'),
            AssociationField::new('line', 'Lines'),
            AssociationField::new('user', 'Users'),
            
        ];
    }
    
}

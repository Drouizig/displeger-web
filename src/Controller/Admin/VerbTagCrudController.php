<?php

namespace App\Controller\Admin;

use App\Entity\VerbTag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class VerbTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VerbTag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('tag'),
            AssociationField::new('sources'),
        ];
    }

}

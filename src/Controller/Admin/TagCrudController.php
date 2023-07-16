<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagCategoryTranslationType;
use App\Form\TagTranslationType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code', 'app.form.source.code'),
            AssociationField::new('category', 'Rummad')->setCrudController(TagCategoryTranslationType::class),
            CollectionField::new('translations',  'app.form.source.translations')->setEntryType(TagTranslationType::class),
        ];
    }

}

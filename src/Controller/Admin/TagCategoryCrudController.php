<?php

namespace App\Controller\Admin;

use App\Entity\TagCategory;
use App\Form\TagCategoryTranslationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TagCategory::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Rummad tikedenn')
            ->setEntityLabelInPlural('Rummad tikedennoù');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code', 'Bonneg'),
            ColorField::new('color', 'Liv'),
            CollectionField::new('translations', 'app.form.source.translations')->setEntryType(TagCategoryTranslationType::class)
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function(TagCategory $tagCategory) {
                    return count($tagCategory->getTags()) == 0;
                });
            })
            ;
    }

}

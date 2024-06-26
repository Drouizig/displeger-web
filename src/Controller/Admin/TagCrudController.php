<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Form\TagCategoryTranslationType;
use App\Form\TagTranslationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Tikedenn')
            ->setEntityLabelInPlural('Tikedennoù')
            ->setEntityPermission('ROLE_ADMIN')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code', 'app.form.source.code'),
            AssociationField::new('category', 'Rummad')->setCrudController(TagCategoryTranslationType::class),
            CollectionField::new('translations',  'app.form.source.translations')->setEntryType(TagTranslationType::class),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function(Tag $tag) {
                    return count($tag->getVerbs()) == 0;
                });
            })
            ;
    }

}

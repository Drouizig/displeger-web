<?php

namespace App\Controller\Admin;

use App\Entity\Verb;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VerbCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verb::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(['localizations.infinitive'])
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName == Crud::PAGE_INDEX) {
            return [
                CollectionField::new('localizations', 'app.form.verb.localizations')->setSortable(true),
                CollectionField::new('translations', 'app.form.verb.translations'),
                CollectionField::new('tags', 'app.form.verb.tags'),
                TextField::new('categories', 'app.form.verb.category')
            ];
        }
        return [
            CollectionField::new('localizations', 'app.form.verb.localizations')->setRequired(true)->useEntryCrudForm()->renderExpanded()
                ->setDefaultColumns('col-md-12'),
            CollectionField::new('descriptionTranslations', 'app.form.verb.descriptions')->useEntryCrudForm()
                ->setDefaultColumns('col-md-12'),
            CollectionField::new('translations', 'app.form.verb.translations')->useEntryCrudForm(null, 'embed_new', 'embed_edit')->renderExpanded()
                ->setDefaultColumns('col-md-12'),
            CollectionField::new('tags', 'app.form.verb.tags')->useEntryCrudForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {

        return parent::configureFilters($filters)
            ;
    }


}

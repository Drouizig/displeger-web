<?php

namespace App\Controller\Admin;

use App\Entity\Verb;
use App\Form\VerbLocalizationType;
use App\Form\VerbTagType;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class VerbCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verb::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(['localizations.infinitive']);
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName == Crud::PAGE_INDEX) {
            return [
                CollectionField::new('localizations', 'app.form.verb.localizations'),
                CollectionField::new('translations', 'app.form.verb.translations'),
                CollectionField::new('tags', 'app.form.verb.tags'),
            ];
        }
        return [
            CollectionField::new('localizations', 'app.form.verb.localizations')->useEntryCrudForm()->renderExpanded(),
            CollectionField::new('descriptionTranslations', 'app.form.verb.descriptions')->useEntryCrudForm(),
            CollectionField::new('translations', 'app.form.verb.translations')->useEntryCrudForm()->renderExpanded(),
            CollectionField::new('tags', 'app.form.verb.tags')->useEntryCrudForm(),
        ];
    }

}

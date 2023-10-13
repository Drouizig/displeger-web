<?php

namespace App\Controller\Admin;

use App\Entity\VerbTranslation;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VerbTranslationCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly ListsUtil $listsUtil
    )
    {
    }
    public static function getEntityFqcn(): string
    {
        return VerbTranslation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('translation', 'app.form.verb.translation'),
            ChoiceField::new('languageCode', 'app.form.verb.language_code')
                ->setChoices(array_flip($this->listsUtil->getLocales())),
            AssociationField::new('sources', 'app.form.verb.sources'),
        ];
    }

}

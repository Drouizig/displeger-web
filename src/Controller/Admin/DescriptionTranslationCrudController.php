<?php

namespace App\Controller\Admin;

use App\Entity\DescriptionTranslation;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DescriptionTranslationCrudController extends AbstractCrudController
{

    public function __construct(
        protected readonly ListsUtil $listsUtil
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return DescriptionTranslation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('content'),
            ChoiceField::new('languageCode')
                ->setChoices(array_flip($this->listsUtil->getLocales())),
            AssociationField::new('sources'),
        ];
    }

}

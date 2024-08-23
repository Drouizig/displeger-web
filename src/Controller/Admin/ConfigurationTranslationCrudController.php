<?php

namespace App\Controller\Admin;

use App\Entity\ConfigurationTranslation;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationTranslationCrudController extends AbstractCrudController
{

    public function __construct(
    protected readonly ListsUtil $listsUtil
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return ConfigurationTranslation::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('locale')
                ->setChoices(array_flip($this->listsUtil->getLocales())),
            TextField::new('title'),
            TextEditorField::new('text')->setColumns(''),
        ];
    }

}

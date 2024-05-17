<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LanguageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{

    public function __construct(
        private ListsUtil $listsUtil
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Implijer')
            ->setEntityLabelInPlural('Implijerien')
            ->setEntityPermission('ROLE_ADMIN')
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username', 'app.form.user.username'),
            TextField::new('plainPassword', 'app.form.user.password')->onlyOnForms(),
            ChoiceField::new('language', 'app.form.verb.language_code')
                ->setChoices(array_flip($this->listsUtil->getLocales())),
            ChoiceField::new('roles', 'app.form.user.roles')
                ->allowMultipleChoices(true)
                ->setChoices(
                    [
                        'Merour' => 'ROLE_ADMIN',
                        'Troer⋅ez' => 'ROLE_TRANSLATOR'
                    ]
                ),
        ];
    }

}

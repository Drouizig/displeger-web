<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addCssFile(Asset::fromEasyAdminAssetPackage('field-text-editor.css'))
            ->addJsFile(Asset::fromEasyAdminAssetPackage('field-text-editor.js'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Testenn')
            ->setEntityLabelInPlural('Testennoù')
            ->setEntityPermission('ROLE_ADMIN')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code'),
            CollectionField::new('translations')->useEntryCrudForm()->setColumns('')->renderExpanded(true),
        ];
    }

}

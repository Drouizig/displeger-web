<?php

namespace App\Controller\Admin;

use App\Entity\Source;
use App\Entity\SourceTypeEnum;
use App\Form\SourceTranslationType;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class SourceCrudController extends AbstractCrudController
{
    /** @var ListsUtil $locales */
    protected $locales;

    public function __construct(ListsUtil $locales)
    {
        $this->locales = $locales;
    }

    public static function getEntityFqcn(): string
    {
        return Source::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Mammenn')
            ->setEntityLabelInPlural('Mammennoù')
            ->setEntityPermission('ROLE_ADMIN')
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        if($pageName === Crud::PAGE_INDEX) {
            return [
                TextField::new('code', 'app.form.source.code'),
                ChoiceField::new('type', 'app.form.source.type')->setChoices(
                    [
                        'app.source.type.'. SourceTypeEnum::GRAMMAR => SourceTypeEnum::GRAMMAR,
                        'app.source.type.'. SourceTypeEnum::TRADUCTION => SourceTypeEnum::TRADUCTION,
                        'app.source.type.'. SourceTypeEnum::VERB => SourceTypeEnum::VERB
                    ]
                ),
                ChoiceField::new('locale','app.form.source.locale')->setChoices(array_flip($this->locales->getLocales())),
                TextField::new('url', 'app.form.source.url'),
                BooleanField::new('active', 'Diskouezet'),
                ];
        } else {
            return [
                TextField::new('code', 'app.form.source.code'),
                ChoiceField::new('type', 'app.form.source.type')->setChoices(
                    [
                        'app.source.type.'. SourceTypeEnum::GRAMMAR => SourceTypeEnum::GRAMMAR,
                        'app.source.type.'. SourceTypeEnum::TRADUCTION => SourceTypeEnum::TRADUCTION,
                        'app.source.type.'. SourceTypeEnum::VERB => SourceTypeEnum::VERB
                    ]
                ),
                ChoiceField::new('locale','app.form.source.locale')->setChoices(array_flip($this->locales->getLocales())),
                TextField::new('url', 'app.form.source.url'),
                BooleanField::new('active', 'app.form.source.active'),
                CollectionField::new('translations', 'app.form.source.translations')->setEntryType(SourceTranslationType::class)
            ];
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(ChoiceFilter::new('locale')->setChoices(
                [
                    'Français' => 'fr',
                    'Brezhoneg' => 'br',
                    'English' => 'en'
                ]
            ))
            ;
    }

}

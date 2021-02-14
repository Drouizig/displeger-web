<?php

namespace App\Form;

use App\Entity\TagCategory;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TagCategoryType extends AbstractType
{
    /** @var ListsUtil $locales */
    protected $locales;

    public function __construct(ListsUtil $locales)
    {
        $this->locales = $locales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', null, [
                'label' => 'Bonneg'
            ])
            ->add('color', ColorType::class, [
                'label' => 'Liv'
            ])
            ->add('translations', CollectionType::class, [
                'label' => 'app.form.source.translations',
                'entry_type' => TagCategoryTranslationType::class,
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'app.form.verb.save'
            ])
            ->add('save_return', SubmitType::class, [
                'label' => 'app.form.verb.save_return'
            ])
            ->add('save_continue', SubmitType::class, [
                'label' => 'app.form.verb.save_continue'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TagCategory::class,
        ]);
    }
}

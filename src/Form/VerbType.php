<?php

namespace App\Form;

use App\Entity\Verb;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\DescriptionTranslationType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VerbType extends AbstractType
{
    /** @var ListsUtil $locales */
    protected $listsUtil;

    public function __construct(ListsUtil $listsUtil) 
    {
        $this->listsUtil = $listsUtil;
    } 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('localizations', CollectionType::class, [
                'label' => 'app.form.verb.localizations',
                'entry_type' => VerbLocalizationType::class,
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('descriptionTranslations', CollectionType::class, [
                'label' => 'app.form.verb.descriptions',
                'entry_type' => DescriptionTranslationType::class,
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('translations', CollectionType::class, [
                'label' => 'app.form.verb.translations',
                'entry_type' => VerbTranslationType::class,
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('tags', CollectionType::class, [
                'label' => 'app.form.verb.tags',
                'entry_type' => VerbTagType::class,
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'app.form.verb.active',
                'required' => false
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
            'data_class' => Verb::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Source;
use App\Entity\SourceTypeEnum;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SourceType extends AbstractType
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
                'label' => 'app.form.source.code'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'app.form.source.type',
                'choices' => [
                    'app.source.type.'. SourceTypeEnum::GRAMMAR => SourceTypeEnum::GRAMMAR,
                    'app.source.type.'. SourceTypeEnum::TRADUCTION => SourceTypeEnum::TRADUCTION,
                    'app.source.type.'. SourceTypeEnum::VERB => SourceTypeEnum::VERB
                ],
                'required' => false
            ])
            ->add('locale', ChoiceType::class, [
                'label' => 'app.form.source.locale',
                'choices' => array_flip($this->locales->getLocales()),
                'required' => false
                ]
            )
            ->add('url', null, [
                'label' => 'app.form.source.url',
                'help' => 'app.form.source.url.help'
            ])
            ->add('translations', CollectionType::class, [
                'label' => 'app.form.source.translations',
                'entry_type' => SourceTranslationType::class,
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
            'data_class' => Source::class,
        ]);
    }
}

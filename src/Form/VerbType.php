<?php

namespace App\Form;

use App\Entity\Verb;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VerbType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('localizations', CollectionType::class, [
                'label' => 'app.form.verb.localizations',
                'entry_type' => VerbLocalizationType::class,
            ])
            ->add('translations', CollectionType::class, [
                'label' => 'app.form.verb.translations',
                'entry_type' => VerbTranslationType::class,
            ])
            ->add('category', null, [
                'label' => 'app.form.verb.category'
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

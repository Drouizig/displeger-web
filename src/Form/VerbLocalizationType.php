<?php

namespace App\Form;

use App\Entity\VerbLocalization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbLocalizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('infinitive', null, [
                'label' => 'app.form.verb.localizations'
            ])
            ->add('base', null, [
                'label' => 'app.form.verb.localizations'
            ])
            ->add('dialectCode', null, [
                'label' => 'app.form.verb.translations'
            ])
            ->add('sources', null, [
                'label' => 'app.form.verb.category'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VerbLocalization::class,
        ]);
    }
}

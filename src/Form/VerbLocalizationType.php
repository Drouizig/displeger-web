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
                'label' => 'app.form.verb.infinitive'
            ])
            ->add('base', null, [
                'label' => 'app.form.verb.base'
            ])
            ->add('dialectCode', null, [
                'label' => 'app.form.verb.dialect_code'
            ])
            ->add('sources', null, [
                'label' => 'app.form.verb.sources'
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

<?php

namespace App\Form;

use App\Entity\VerbTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translation', null, [
                'label' => 'app.form.verb.translation'
            ])
            ->add('languageCode', null, [
                'label' => 'app.form.verb.language_code'
            ])
            ->add('sources', null, [
                'label' => 'app.form.verb.sources'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VerbTranslation::class,
        ]);
    }
}

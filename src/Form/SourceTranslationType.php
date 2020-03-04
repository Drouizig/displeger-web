<?php

namespace App\Form;

use App\Entity\SourceTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, [
                'label' => 'app.form.source.label'
            ])
            ->add('description', null, [
                'label' => 'app.form.source.description'
            ])
            ->add('languageCode', null, [
                'label' => 'app.form.source.language_code'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SourceTranslation::class,
        ]);
    }
}

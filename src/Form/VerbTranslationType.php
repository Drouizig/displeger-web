<?php

namespace App\Form;

use App\Entity\VerbTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translation', null, [
                'label' => 'app.form.verb.translation'
            ])
            ->add('languageCode', ChoiceType::class, [
                'label' => 'app.form.verb.language_code',
                'choices' => array_flip(Intl::getLocaleBundle()->getLocaleNames()),
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

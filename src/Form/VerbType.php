<?php

namespace App\Form;

use App\Entity\Verb;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VerbType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('anvVerb', null, [
                'label' => 'app.form.verb.anvVerb'
            ])
            ->add('pennrann', null, [
                'label' => 'app.form.verb.pennrann'
            ])
            ->add('category', null, [
                'label' => 'app.form.verb.category'
            ])
            ->add('galleg', null, [
                'label' => 'app.form.verb.galleg'
            ])
            ->add('saozneg', null, [
                'label' => 'app.form.verb.saozneg'
            ])->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Verb::class,
        ]);
    }
}

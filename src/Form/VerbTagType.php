<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\VerbTag;
use App\Entity\VerbTranslation;
use App\Util\ListsUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbTagType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', EntityType::class, [
                'label' => 'Tikedenn',
                'class' => Tag::class,
                'required' => true]
                )
            ->add('sources', null, [
                'label' => 'app.form.verb.sources'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VerbTag::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedSearchType extends AbstractType
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
        ->add('term', TextType::class)
        ->add('language', ChoiceType::class, [
                'label' => 'app.form.source.language_code',
                'choices' => array_flip($this->locales->getLocales()),
            ])
        ->add('conjugated', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

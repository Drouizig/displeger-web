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
        ->add('term_advanced', TextType::class, [
            'label' => 'app.form.search.term',
        ])
        ->add('language', ChoiceType::class, [
                'label' => 'app.form.search.language_code',
                'choices' => array_flip($this->locales->getLocales()),
                'preferred_choices' => ['br', 'fr', 'en'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return "";
    }
}

<?php

namespace App\Form;

use App\Util\ListsUtil;
use App\Entity\ConfigurationTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ConfigurationTranslationType extends AbstractType
{
    /** @var ListsUtil $listsUtil */
    protected $listsUtil;

    public function __construct(ListsUtil $listsUtil) 
    {
        $this->listsUtil = $listsUtil;
    } 
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', ChoiceType::class, [
                'label' => 'app.form.verb.language_code',
                'choices' => array_flip($this->listsUtil->getLocales()),
                'preferred_choices' => ['br', 'fr', 'en'],
            ])
            ->add('title', TextType::class)
            ->add('text')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigurationTranslation::class,
        ]);
    }
}

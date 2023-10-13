<?php

namespace App\Form;

use App\Entity\SourceTranslation;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceTranslationType extends AbstractType
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
            ->add('label', null, [
                'label' => 'app.form.source.label'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.form.source.description'
            ])
            ->add('languageCode', ChoiceType::class, [
                'label' => 'app.form.source.language_code',
                'choices' => array_flip($this->locales->getLocales()),
                'preferred_choices' => ['br', 'fr', 'en'],
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

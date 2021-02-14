<?php

namespace App\Form;

use App\Entity\TagCategoryTranslation;
use App\Entity\TagTranslation;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagCategoryTranslationType extends AbstractType
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
            ->add('description', null, [
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
            'data_class' => TagCategoryTranslation::class,
        ]);
    }
}

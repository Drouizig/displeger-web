<?php

namespace App\Form;

use App\Entity\VerbTranslation;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbTranslationType extends AbstractType
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
            ->add('translation', TextType::class, [
                'label' => 'app.form.verb.translation'
            ])
            ->add('languageCode', ChoiceType::class, [
                'label' => 'app.form.verb.language_code',
                'choices' => array_flip($this->listsUtil->getLocales()),
                'preferred_choices' => ['br', 'fr', 'en'],
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

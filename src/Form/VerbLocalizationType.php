<?php

namespace App\Form;

use App\Entity\VerbLocalization;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerbLocalizationType extends AbstractType
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
            ->add('infinitive', null, [
                'label' => 'app.form.verb.infinitive'
            ])
            ->add('base', null, [
                'label' => 'app.form.verb.base'
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'app.form.verb.category',
                'choices' => array_flip($this->listsUtil->getCategories()),
                'preferred_choices' => ['fr', 'en'],
            ])
            ->add('dialectCode', ChoiceType::class, [
                'label' => 'app.form.verb.dialect_code',
                'required' => false,
                'choices' => array_flip($this->listsUtil->getDialects()),
                'multiple' => true
            ])
            ->add('sources', null, [
                'label' => 'app.form.verb.sources'
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $verbLocalisation = $event->getData();
            $form = $event->getForm();

            if(null !== $verbLocalisation && substr($verbLocalisation->getBase(), 0, 1) === 'g') {
                $form->add('gouMutation', null, [
                    'label' => 'app.form.verb.gouMutation'
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VerbLocalization::class,
        ]);
    }
}

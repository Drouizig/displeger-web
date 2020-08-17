<?php

namespace App\Form;

use App\Entity\Tag;
use App\Util\ListsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType as NativeSearchType;

class VerbTagType extends AbstractType
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
            ->add('id', ChoiceType::class, [
                'label' => 'app.form.tag.code'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}

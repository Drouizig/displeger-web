<?php

namespace App\Controller\Admin;

use App\Entity\Verb;
use App\Form\VerbLocalizationType;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class VerbCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verb::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            CollectionField::new('localizations')->setEntryType(VerbLocalizationType::class)->renderExpanded(),
        ];
    }

}

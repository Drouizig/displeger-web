<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Verb;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class TranslatorVerbCrudController extends AbstractCrudController
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Verb::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(['localizations.infinitive'])
            ->setEntityPermission('ROLE_TRANSLATOR')
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            CollectionField::new('localizations', 'app.form.verb.localizations')->setSortable(true),
            CollectionField::new('translations', 'app.form.verb.translations'),
            //TODO displat if the translation has been done or not
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var User $user */
        $user = $this->getUser();
        $actions->remove(Action::INDEX, Action::NEW);
        $actions->remove(Action::INDEX, Action::EDIT);
        $actions->remove(Action::INDEX, Action::DELETE);

        $actionEditTranslate = Action::new('edit_translation', 'Edit translation');
        $actionEditTranslate->displayIf(function(Verb $verb) use ($user) {
            return $verb->hasTranslationInLanguage($user->getLanguage());
        });
        $actionEditTranslate->linkToUrl(function (Verb $verb) use ($user) {

            $urlGenerator = $this->adminUrlGenerator
                ->setController(VerbTranslationCrudController::class)
            ;
            $urlGenerator->setEntityId($verb->getTranslation($user->getLanguage())->getId());
            $urlGenerator->setAction(Action::EDIT);
            return $urlGenerator->generateUrl();
        });
        $actions->add(Action::INDEX, $actionEditTranslate);


        $actionCreateTranslate = Action::new('create_translation', 'Create translation');
        $actionCreateTranslate->displayIf(function(Verb $verb) use ($user) {
            return !$verb->hasTranslationInLanguage($user->getLanguage());
        });
        $actionCreateTranslate->linkToUrl(function (Verb $verb) use ($user) {

            $urlGenerator = $this->adminUrlGenerator
                ->setController(VerbTranslationCrudController::class)
            ;
            $urlGenerator->setAction(Action::NEW);
            $urlGenerator->set('verbId', $verb->getId());
            return $urlGenerator->generateUrl();
        });
        $actions->add(Action::INDEX, $actionCreateTranslate);
        return $actions;
    }


}

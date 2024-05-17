<?php

namespace App\Controller\Admin;

use App\Entity\VerbTranslation;
use App\Repository\VerbRepository;
use App\Util\ListsUtil;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LanguageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use function Symfony\Component\String\s;

class VerbTranslationCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly ListsUtil $listsUtil,
        protected readonly VerbRepository $verbRepository,
        protected readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];

        if ('saveAndReturn' === $submitButtonName) {
            $url = $this->adminUrlGenerator
                ->unset('entityId')
                ->setController(TranslatorVerbCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    public function configureCrud(Crud $crud): Crud
    {
        parent::configureCrud($crud);
        $crud->setPageTitle(Action::NEW, function () {

            if ($this->getContext()->getRequest()->get('verbId') !== null) {
                $verbId = $this->getContext()->getRequest()->get('verbId');
                $verb = $this->verbRepository->find($verbId);
                return sprintf('Translate "%s" in %s', $verb, $this->listsUtil->getLocales('en')[$this->getUser()->getLanguage()]);
            }
        });

        return $crud;
    }


    public static function getEntityFqcn(): string
    {
        return VerbTranslation::class;
    }

    public function configureFields(string $pageName): iterable
    {

        if ($this->getContext()->getRequest()->get('verbId') !== null) {
            $verbId = $this->getContext()->getRequest()->get('verbId');
            $verb = $this->verbRepository->find($verbId);
        }

        if($pageName === 'embed_new' || $pageName === 'embed_edit') {
            return [
                TextField::new('translation', 'app.form.verb.translation'),
                ChoiceField::new('languageCode', 'app.form.verb.language_code')
                    ->setChoices(array_flip($this->listsUtil->getLocales())),
                AssociationField::new('sources', 'app.form.verb.sources'),
            ];
        }
        if($pageName === 'edit') {

            return [
                TextField::new('verb')->setDisabled(),
                TextField::new('languageCode')->setDisabled(),
                TextareaField::new('translation', 'Translation'),
                AssociationField::new('sources', 'Sources'),
            ];
        }
        return [
            HiddenField::new('verb')->setEmptyData($verb),
            HiddenField::new('languageCode', 'language')
                ->setFormTypeOptions(['data' => $this->getUser()->getLanguage()]),
            TextareaField::new('translation'),
            AssociationField::new('sources')
        ];
    }

}

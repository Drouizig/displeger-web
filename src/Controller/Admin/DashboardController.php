<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Entity\Source;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\User;
use App\Entity\Verb;
use App\Util\StatisticsManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private StatisticsManager $statisticsManager,
        private AdminUrlGenerator $adminUrlGenerator)
    {
    }


    /**
     * @Route("/ezadmin", name="ezadmin")
     */
    public function index(): Response
    {
        return $this->render('admin/easyadmin/home.html.twig',
        ['statisticsManager' => $this->statisticsManager]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lodenn verañ')

            ;
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()->showEntityActionsInlined(true);
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::linkToCrud('Verboù', 'fas fa-star', Verb::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToCrud('Mammennoù', 'fas fa-book', Source::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToCrud('Tikedennoù', 'fas fa-tag', Tag::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToCrud('Rummadoù tikedennoù', 'fas fa-tag', TagCategory::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToCrud('Testennoù al lec’hienn', 'fas fa-scroll', Configuration::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToCrud('Implijerien', 'fas fa-user', User::class)->setPermission('ROLE_ADMIN');
         yield MenuItem::linkToUrl(
             'Troidigezhioù',
             'fas fa-language',
             $this->adminUrlGenerator->setController(TranslatorVerbCrudController::class)->setAction(Action::INDEX)->generateUrl()
         )->setPermission('ROLE_TRANSLATOR');;
         yield MenuItem::linkToRoute('Lec’hienn', 'fas fa-earth', 'pre_locale', ['target'=> '_blank']);
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('chart');

        }
}

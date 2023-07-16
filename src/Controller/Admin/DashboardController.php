<?php

namespace App\Controller\Admin;

use App\Entity\Source;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\User;
use App\Entity\Verb;
use App\Util\StatisticsManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $statisticsManager;
    public function __construct(StatisticsManager $statisticsManager)
    {
        $this->statisticsManager = $statisticsManager;
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
         yield MenuItem::linkToCrud('Verboù', 'fas fa-star', Verb::class);
         yield MenuItem::linkToCrud('Mammennoù', 'fas fa-book', Source::class);
         yield MenuItem::linkToCrud('Tikedennoù', 'fas fa-tag', Tag::class);
         yield MenuItem::linkToCrud('Rummadoù tikedennoù', 'fas fa-tag', TagCategory::class);
         yield MenuItem::linkToCrud('Implijerien', 'fas fa-user', User::class);
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('chart');

        }
}

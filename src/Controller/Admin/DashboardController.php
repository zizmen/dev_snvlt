<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Securite\OptionCrudController;
use App\Entity\Admin\Option;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private  AdminUrlGenerator $adminUrlGenerator)
    {

    }

    #[Route('/admin-snvlt', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(OptionCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SNVLT ADMIN');
    }
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUserIdentifier())
            ->setGravatarEmail($user->getEmail())
            ->setAvatarUrl('https://127.0.0.1:8000/images/uploads/users/' . $user->getPhoto())
            ->displayUserAvatar(true);

    }
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('public/assets/css/admin.css');
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::subMenu('Confriguration', 'fas fa-tools')->setSubItems([
            MenuItem::linkToCrud('Options','fas fa-cog',Option::class)
        ]);
    }
}

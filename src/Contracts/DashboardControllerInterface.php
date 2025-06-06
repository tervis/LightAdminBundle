<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Contracts;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Tervis\LightAdminBundle\Dto\UserMenu;

/**
 * This must be implemented by all backend dashboards.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
interface DashboardControllerInterface
{
    // public function configureDashboard(): Dashboard;

    // public function configureAssets(): Assets;

    /**
     * @return MenuItemInterface[]
     */
    public function configureMenuItems(): iterable;


    public function configureUserMenu(UserInterface $user): UserMenu;

    // public function configureCrud(): Crud;

    // public function configureActions(): Actions;

    // public function configureFilters(): Filters;

    public function index(): Response;
}

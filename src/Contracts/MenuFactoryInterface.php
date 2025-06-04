<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Contracts;

use Symfony\Component\Security\Core\User\UserInterface;
use Tervis\LightAdminBundle\Contracts\MenuItemInterface;
use Tervis\LightAdminBundle\Dto\MainMenu;
use Tervis\LightAdminBundle\Dto\UserMenu;

interface MenuFactoryInterface
{
    /**
     * Configure main menu items 
     *
     * @return array
     */
    public static function configureMenuItems(): array;

    /**
     * Configure user menu items
     *
     * @param UserInterface $user
     * @return array
     */
    public static function configureUserMenuItems(UserInterface $user): array;

    /**
     * Creates the main menu by processing the given list of menu items
     *
     * @return MainMenu
     */
    public static function createMainMenu(): MainMenu;


    /**
     * Creates the menu of user actions displayed as a dropdown at the top
     * of the page and associated to the name of the currently logged in user.
     *
     * @param UserInterface $user
     * @return UserMenu
     */
    public static function createUserMenu(UserInterface $user): UserMenu;
}

<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Twig\Runtime;

use App\Menu\MenuFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Environment;

class LightAdminTwigExtensionRuntime implements RuntimeExtensionInterface
{
    private Environment $twigEnvironment; // Declare the Twig Environment

    public function __construct(Environment $twigEnvironment) // Inject it
    {
        $this->twigEnvironment = $twigEnvironment; // Assign it
    }

    public function renderMainMenu(string $currentPath): string
    {
        if (!class_exists(MenuFactory::class)) {
            return '';
        }

        $mainMenu = MenuFactory::createMainMenu();

        return $this->twigEnvironment->render('@LightAdmin/menus/main_menu.html.twig', ['menu' => $mainMenu, 'currentPath' => $currentPath]);
    }

    public function renderUserMenu(?UserInterface $user = null): string
    {
        if (!class_exists(MenuFactory::class) || !$user) {
            return '';
        }

        $userMenu = MenuFactory::createUserMenu($user);

        return $this->twigEnvironment->render('@LightAdmin/menus/user_menu.html.twig', [
            'userMenu' => $userMenu,
        ]);
    }
}

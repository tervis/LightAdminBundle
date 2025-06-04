<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Menu;

use Tervis\LightAdminBundle\Dto\MenuItem;

final class MenuBuilder
{

    public static function linkToCrud(string $label, ?string $icon = null): MenuItem
    {
        return (new MenuItem())->setLabel($label)->setIcon($icon);
    }


    public static function linkToDashboard(string $label, ?string $icon = null): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_DASHBOARD))->setLabel($label)->setIcon($icon);
    }


    public static function linkToExitImpersonation(string $label, ?string $icon = null): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_EXIT_IMPERSONATION))->setLabel($label)->setIcon($icon);
    }


    public static function linkToLogout(string $label, ?string $icon = null): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_LOGOUT))->setLabel($label)->setIcon($icon)->setRouteName('app_logout');
    }

    public static function linkToRoute(string $label, string $routeName, array $routeParameters = []): MenuItem
    {
        return (new MenuItem())->setLabel($label)->setRouteName($routeName)->setRouteParameters($routeParameters);
    }


    public static function linkToUrl(string $label, string $url, ?string $icon): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_URL))->setLabel($label)->setIcon($icon)->setLinkUrl($url);
    }


    public static function section(?string $label = null, ?string $icon = null): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_SECTION))->setLabel($label)->setIcon($icon);
    }


    public static function subMenu(string $label, ?string $icon = null): MenuItem
    {
        return (new MenuItem(MenuItem::TYPE_SUBMENU))->setLabel($label)->setIcon($icon);
    }
}

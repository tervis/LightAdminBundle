<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class LightAdminTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    /**
     * Checks if the given route name matches the currently active route.
     * This function implicitly handles routes with parameters as it only compares the route name,
     * not the full URL path. For example, if the current route is 'app_product_show' (for /products/123),
     * calling is_active_route('app_product_show') will return a string 'active'.
     * 
     * @param string $routeName The name of the route to check against (e.g., 'app_home', 'product_detail').
     * @param string $activeClass Defaults to 'active'
     * @return string Return 'active' or empty if not active
     */
    public function isActiveRoute(string $routeName, string $activeClass = 'active'): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return '';
        }

        // Get the name of the currently matched route from the request attributes.
        $currentRoute = $request->attributes->get('_route');

        return $currentRoute === $routeName ? $activeClass : '';
    }

    /**
     * Checks if the currently active route name starts with the given prefix.
     * This is useful for highlighting entire sections of a navigation (e.g., 'admin_' for all admin routes).
     *
     * @param string $prefix The prefix to check against (e.g., 'admin_', 'blog_').
     * @param string $activeClass Defaults to 'active'
     * @return string Return 'active' or empty if not active
     */
    public function isActiveRoutePrefix(string $prefix, string $activeClass = 'active'): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return '';
        }

        $currentRoute = $request->attributes->get('_route');

        // Check if the current route name starts with the provided prefix.
        return (is_string($currentRoute) && str_starts_with($currentRoute, $prefix)) ? $activeClass : '';
    }
}

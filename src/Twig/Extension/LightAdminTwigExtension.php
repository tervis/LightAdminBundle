<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Twig\Extension;

use Tervis\Bundle\LightAdminBundle\Twig\Runtime\LightAdminTwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LightAdminTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_route', [LightAdminTwigExtensionRuntime::class, 'isActiveRoute']),
            new TwigFunction('is_active_route_prefix', [LightAdminTwigExtensionRuntime::class, 'isActiveRoutePrefix']),
        ];
    }
}

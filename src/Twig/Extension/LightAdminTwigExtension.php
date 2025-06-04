<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Twig\Extension;

use Tervis\LightAdminBundle\Twig\Runtime\LightAdminTwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LightAdminTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('main_menu', [LightAdminTwigExtensionRuntime::class, 'renderMainMenu'], ['is_safe' => ['html']]),
            new TwigFunction('user_menu', [LightAdminTwigExtensionRuntime::class, 'renderUserMenu'], ['is_safe' => ['html']]),
        ];
    }
}

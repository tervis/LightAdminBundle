<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use Symfony\Component\DependencyInjection\Reference;
use Tervis\LightAdminBundle\Twig\Extension\LightAdminTwigExtension;
use Tervis\LightAdminBundle\Maker\MakeAdminCrudController;
use Tervis\LightAdminBundle\Maker\MakeAdminDashboard;
use Tervis\LightAdminBundle\Twig\Runtime\LightAdminTwigExtensionRuntime;

return static function (ContainerConfigurator $container) {
    $services = $container->services()->defaults()->private()
        //->instanceof(FieldConfiguratorInterface::class)->tag(EasyAdminExtension::TAG_FIELD_CONFIGURATOR)
        //->instanceof(FilterConfiguratorInterface::class)->tag(EasyAdminExtension::TAG_FILTER_CONFIGURATOR)
    ;
    $services
        ->set(LightAdminTwigExtension::class)->public()
        ->tag('twig.extension')
    ;
    $services
        ->set(LightAdminTwigExtensionRuntime::class)->public()
        ->arg(0, new Reference('twig'))
        ->tag('twig.runtime')
    ;
    $services
        ->set(MakeAdminCrudController::class)->public()
        ->arg(0, service('maker.doctrine_helper'))
        ->arg(1, service('maker.renderer.form_type_renderer'))
        ->tag('maker.command')
    ;

    $services
        ->set(MakeAdminDashboard::class)->public()
        ->tag('maker.command')
    ;
};

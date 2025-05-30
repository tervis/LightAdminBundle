<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\HttpKernel\KernelInterface;
use Tervis\Bundle\LightAdminBundle\Command\MakeAdminDashboardCommand;
use Tervis\Bundle\LightAdminBundle\Command\MakeCrudControllerCommand;
use Tervis\Bundle\LightAdminBundle\Form\CrudFormType;
use Tervis\Bundle\LightAdminBundle\Maker\ClassMaker;
use Tervis\Bundle\LightAdminBundle\Twig\Components\Alert;
use Tervis\Bundle\LightAdminBundle\Twig\Components\SwitchButton;

return static function (ContainerConfigurator $container) {

    $container->services()
        ->set(MakeAdminDashboardCommand::class)->public()
        ->arg(0, service(ClassMaker::class))
        ->arg(1, param('kernel.project_dir'))
        ->tag('console.command')

        ->set(MakeCrudControllerCommand::class)->public()
        ->arg(0, param('kernel.project_dir'))
        ->arg(1, service(ClassMaker::class))
        ->arg(2, service('doctrine'))
        ->tag('console.command')

        ->set(ClassMaker::class)
        ->arg(0, service(KernelInterface::class))
        ->arg(1, param('kernel.project_dir'))

        ->set(CrudFormType::class)
        ->arg(0, service('form.type_guesser.doctrine'))
        ->tag('form.type', ['alias' => 'ea_crud'])

        ->set(Alert::class)
        ->tag('twig.component')

        ->set(SwitchButton::class)
        ->tag('twig.component')
    ;
};

<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

use Tervis\Bundle\LightAdminBundle\Maker\MakeAdminDashboard;
use Tervis\Bundle\LightAdminBundle\Maker\MakeCrudController;
use Tervis\Bundle\LightAdminBundle\Form\CrudFormType;
use Tervis\Bundle\LightAdminBundle\Maker\ClassMaker;
use Tervis\Bundle\LightAdminBundle\Twig\Components\Alert;
use Tervis\Bundle\LightAdminBundle\Twig\Components\SwitchButton;

use Symfony\Component\HttpKernel\KernelInterface;

class LightAdminBundleExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Tervis\\Bundle\\LightAdminBundle\\Twig\\Component\\' => [
                    'template_directory' => '@LightAdmin/components/',
                    'name_prefix' => 'la',
                ],
            ],
        ]);

        $bundleTemplatesOverrideDir = $builder->getParameter('kernel.project_dir') . '/templates/bundles/LightAdminBundle/';

        $builder->prependExtensionConfig('twig', [
            'paths' => is_dir($bundleTemplatesOverrideDir)
                ? [
                    'templates/bundles/LightAdminBundle/' => 'LightAdmin',
                    \dirname(__DIR__) . '/../templates/' => 'LightAdmin',
                ]
                : [
                    \dirname(__DIR__) . '/../templates/' => 'LightAdmin',
                ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        // $loader = new PhpFileLoader(
        //     $container,
        //     new FileLocator(__DIR__ . '/../Resources/config')
        // );
        // Ladataan myöhemmin palvelutiedostot, reitit jne.
        //$loader->load('services.php');
        // $loader->load('routes.yaml');
        $this->registerBasicServices($container);
    }

    private function registerBasicServices(ContainerBuilder $container): void
    {

        $container
            ->register('light_admin.make_light_admin_crud', MakeCrudController::class)
            ->setArguments([])
            ->addTag('maker.command')
        ;

        $container
            ->register('light_admin.make_light_admin_dashboard', MakeAdminDashboard::class)
            ->setArguments([])
            ->addTag('maker.command')
        ;
    }

    public function getAlias(): string
    {
        return 'light_admin';
    }
}

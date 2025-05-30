<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

use Tervis\Bundle\LightAdminBundle\Command\MakeAdminDashboardCommand;
use Tervis\Bundle\LightAdminBundle\Command\MakeCrudControllerCommand;
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
                    'template_directory' => '@LightAdminBundle/components/',
                    'name_prefix' => 'la',
                ],
            ],
        ]);

        $bundleTemplatesOverrideDir = $builder->getParameter('kernel.project_dir') . '/templates/bundles/LightAdminBundle/';

        $builder->prependExtensionConfig('twig', [
            'paths' => is_dir($bundleTemplatesOverrideDir)
                ? [
                    'templates/bundles/LightAdminBundle/' => 'LightAdminBundle',
                    \dirname(__DIR__) . '/../templates/' => 'LightAdminBundle',
                ]
                : [
                    \dirname(__DIR__) . '/../templates/' => 'LightAdminBundle',
                ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters based on the processed configuration
        // $container->setParameter('tervis_lightadmin_bundle.my_option', $config['my_option']);
        // $container->setParameter('tervis_lightadmin_bundle.another_setting.enabled', $config['another_setting']['enabled']);
        // $container->setParameter('tervis_lightadmin_bundle.another_setting.limit', $config['another_setting']['limit']);

        // Load services from services.yaml
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.php');

        // You can also manipulate service definitions based on configuration
        // Example: if a specific option is enabled, add a tag to a service
        // if ($config['another_setting']['enabled']) {
        //     $definition = $container->getDefinition('MyVendor\MyBundle\Service\MyService');
        //     $definition->addTag('lightadmin_bundle.feature_enabled');
        // }
        //$this->registerBasicServices($container);
    }

    private function registerBasicServices(ContainerBuilder $container): void
    {

        /*
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
*/
        $container
            ->register('light_admin.class_maker', ClassMaker::class)
            ->setArguments([
                KernelInterface::class,
                '%kernel.project_dir%',
            ])
        ;


        $container
            ->register('light_admin_bundle.make_light_admin_crud', MakeCrudControllerCommand::class)
            ->setArguments([
                ClassMaker::class,
            ])
            ->addTag('maker.command')
        ;

        $container
            ->register('light_admin_bundle.make_light_admin_dashboard', MakeAdminDashboardCommand::class)
            ->setArguments([
                ClassMaker::class,
            ])
            ->addTag('maker.command')
        ;



        $container->register('light_admin.components_alert', Alert::class)->addTag('twig.component');
        $container->register('light_admin.components_switch_button', SwitchButton::class)->addTag('twig.component');
    }

    public function getAlias(): string
    {
        return 'light_admin';
    }
}

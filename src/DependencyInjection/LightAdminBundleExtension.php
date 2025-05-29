<?php
// src/MyVendor/MyBundle/DependencyInjection/MyVendorMyBundleExtension.php
namespace tervis\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LightAdminBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters based on the processed configuration
        $container->setParameter('my_vendor_my_bundle.my_option', $config['my_option']);
        $container->setParameter('my_vendor_my_bundle.another_setting.enabled', $config['another_setting']['enabled']);
        $container->setParameter('my_vendor_my_bundle.another_setting.limit', $config['another_setting']['limit']);

        // Load services from services.yaml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // You can also manipulate service definitions based on configuration
        // Example: if a specific option is enabled, add a tag to a service
        if ($config['another_setting']['enabled']) {
            $definition = $container->getDefinition('MyVendor\MyBundle\Service\MyService');
            $definition->addTag('my_bundle.feature_enabled');
        }
    }

    public function getAlias(): string
    {
        return 'tervis_light_admin_bundle';
    }
}

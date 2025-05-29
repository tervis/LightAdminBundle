<?php
// src/MyVendor/MyBundle/DependencyInjection/Configuration.php
namespace MyVendor\MyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('my_vendor_my_bundle');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('my_option')
                    ->defaultValue('default_value')
                    ->info('A customizable option for MyBundle')
                ->end()
                ->arrayNode('another_setting')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->integerNode('limit')->defaultValue(10)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('iliev_symfony_schema');
        $rootNode->addDefaultsIfNotSet();
        
        $this->addDatabaseSection($rootNode);
        $this->addDirectorySection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    private function addDatabaseSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('database')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('orm')
                            ->cannotBeEmpty()
                            ->defaultValue('doctrine')
                            ->validate()
                            ->ifNotInArray(array('doctrine', 'propel'))
                                ->thenInvalid('ORM "%s" is not supported')
                            ->end()
                        ->end()
                        ->scalarNode('table_name')
                            ->cannotBeEmpty()
                            ->defaultValue('model_version')
                            ->info('A database table name used to track the applied SQL files')
                        ->end()
                        ->scalarNode('default_connection')
                            ->cannotBeEmpty()
                            ->defaultValue('default')
                            ->info('A database connection name that is used to execute the SQL queries')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
     */
    private function addDirectorySection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('working_path')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/../schema/sql/updates')
                    ->info('Path to the directory containing the sql files')
                ->end()
            ->end()
        ;
    }
}

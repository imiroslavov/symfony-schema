<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Iliev\SymfonySchemaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class IlievSymfonySchemaExtension extends Extension 
{

    public function load(array $configs, ContainerBuilder $container) 
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('iliev_symfony_schema.database.table_name', $config['database']['table_name']);
        $container->setParameter('iliev_symfony_schema.database.orm', $config['database']['orm']);
        $container->setParameter('iliev_symfony_schema.database.default_connection', $config['database']['default_connection']);
        $container->setParameter('iliev_symfony_schema.working_path', $config['working_path']);
    }
}

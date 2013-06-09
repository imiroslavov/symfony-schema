<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\Connection\Adapter;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class DoctrineAdapter extends ConnectionAdapter
{
    public function initializeParameterBag()
    {
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection = $this->getContainer()->get(sprintf('doctrine.dbal.%s_connection', $this->getConnectionName()));
        
        $this->getParameterBag()->set('username', $connection->getUsername());
        $this->getParameterBag()->set('password', $connection->getPassword());
        $this->getParameterBag()->set('database', $connection->getDatabase());
        
        $this->getParameterBag()->set('host', $connection->getHost());
        $this->getParameterBag()->set('port', $connection->getPort());
        
        $this->getParameterBag()->set('driver', $connection->getDriver()->getName());
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function createConnection()
    {
        $connectionFactory = $this->getContainer()->get('doctrine.dbal.connection_factory');
        
        return $connectionFactory->createConnection(
            array(
                'driver'   => $this->getParameterBag()->get('driver'),
                'user'     => $this->getParameterBag()->get('username'),
                'password' => $this->getParameterBag()->get('password'),
                'host'     => $this->getParameterBag()->get('host'),
                'port'     => $this->getParameterBag()->get('port'),
                'dbname'   => $this->getParameterBag()->get('database'),
            )
        );
    }
}

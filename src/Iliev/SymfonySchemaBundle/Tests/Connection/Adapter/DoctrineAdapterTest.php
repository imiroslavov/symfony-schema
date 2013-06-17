<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Iliev\SymfonySchemaBundle\Tests\Connection\Adapter;

use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Symfony\Component\DependencyInjection\Container;
use Iliev\SymfonySchemaBundle\Connection\Adapter\DoctrineAdapter;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class DoctrineAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    
    public function configuration()
    {
        $data = array(
            'user' => 'test_username',
            'password' => 'test_password',
            'host' => 'test_hostname',
            'dbname' => 'test_database',
            'port' => 1337,
        );

        return new MockDoctrineConection($data, new Driver());
    }
    
    public function setUp()
    {
        if (!class_exists('Doctrine\DBAL\Connection')) {
            $this->markTestSkipped('Doctrine not installed');
        }
        
        $configuration = $this->configuration();

        $container = new Container();
        $container->set('doctrine.dbal.test1_connection', $configuration);

        $this->adapter = new DoctrineAdapter($container);
    }

    public function tearDown()
    {
        unset($this->adapter);
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\DoctrineAdapter::initializeParameterBag
     * @group initializeParameterBag
     */
    public function testInitializeParameterBag1()
    {
        $this->adapter->setConnectionName('test1');
        $this->adapter->initializeParameterBag();

        $this->assertEquals('pdo_mysql', $this->adapter->getParameterBag()->get('driver'));
        $this->assertEquals('test_username', $this->adapter->getParameterBag()->get('username'));
        $this->assertEquals('test_password', $this->adapter->getParameterBag()->get('password'));
        $this->assertEquals('test_database', $this->adapter->getParameterBag()->get('database'));
        $this->assertEquals('test_hostname', $this->adapter->getParameterBag()->get('host'));
        $this->assertEquals('1337', $this->adapter->getParameterBag()->get('port'));
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\DoctrineAdapter::initializeParameterBag
     * @group initializeParameterBag
     * 
     * @expectedException Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testInitializeParameterBag2()
    {
        $this->adapter->setConnectionName('test2');
        $this->adapter->initializeParameterBag();
    }
}

/**
 * @codeCoverageIgnoreStart
 */

class MockDoctrineConection
{
    protected $data;
    protected $driver;

    public function __construct($data, Driver $driver)
    {
        $this->data = $data;
        $this->driver = $driver;
    }

    public function getUsername()
    {
        return $this->data['user'];
    }

    public function getPassword()
    {
        return $this->data['password'];
    }
    
    public function getDatabase()
    {
        return $this->data['dbname'];
    }
    
    public function getHost()
    {
        return $this->data['host'];
    }
    
    public function getPort()
    {
        return $this->data['port'];
    }
    
    public function getDriver()
    {
        return $this->driver;
    }
}

/**
 * @codeCoverageIgnoreEnd
 */

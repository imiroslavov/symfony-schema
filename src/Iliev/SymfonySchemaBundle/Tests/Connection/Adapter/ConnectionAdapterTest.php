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

use Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter;
use Symfony\Component\DependencyInjection\Container;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class ConnectionAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::__construct
     */
    public function testConstructor()
    {
        $this->testSetContainer();
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::getContainer
     */
    public function testGetContainer()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Container', $adapter->__getContainer());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::setContainer
     */
    public function testSetContainer()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->setContainer(new TestContainer());

        $this->assertInstanceOf('Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\TestContainer', $adapter->__getContainer());
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::getParameterBag
     */
    public function testGetParameterBag()
    {
        $adapter = new TestableConnectionAdapter(new Container());

        $this->assertInstanceOf('Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag', $adapter->getParameterBag());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::setConnectionName
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::getConnectionName
     */
    public function testSetConnectionName()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->setConnectionName('connection_name');
        
        $this->assertEquals('connection_name', $adapter->getConnectionName());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::getConnectionName
     * 
     * @expectedException \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\TestParameterBagException
     */
    public function testGetConnectionName()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->initialize('connection_name');
        
        $this->assertEquals('connection_name', $adapter->getConnectionName());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter::initialize
     * 
     * @expectedException \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\TestParameterBagException
     */
    public function testInitialize()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->initialize('connection_name');
    }
}

class TestableConnectionAdapter extends ConnectionAdapter
{
    public function __getContainer()
    {
        return parent::getContainer();
    }

    public function getConnectionName()
    {
        return parent::getConnectionName();
    }
    
    public function initializeParameterBag()
    {
        throw new TestParameterBagException();
    }
    
    public function createConnection()
    {
    }
}

class TestContainer extends Container
{
}

class TestParameterBagException extends \Exception
{
}

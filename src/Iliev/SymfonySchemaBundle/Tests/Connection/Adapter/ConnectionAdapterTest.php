<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
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
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\ConnectionAdapter::__contruct
     */
    public function testConstructor()
    {
        $this->testSetContainer();
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\ConnectionAdapter::getContainer
     */
    public function testGetContainer()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Container', $adapter->__getContainer());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\ConnectionAdapter::setContainer
     */
    public function testSetContainer()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->setContainer(new TestContainer());

        $this->assertInstanceOf('Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\TestContainer', $adapter->__getContainer());
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\ConnectionAdapter::getParameterBag
     */
    public function testGetParameterBag()
    {
        $adapter = new TestableConnectionAdapter(new Container());

        $this->assertInstanceOf('Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag', $adapter->getParameterBag());
    }

    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\ConnectionAdapter::getConnectionName
     * 
     * @expectedException \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\TestParameterBagException
     */
    public function testGetConnectionName()
    {
        $adapter = new TestableConnectionAdapter(new Container());
        $adapter->initialize('connection_name');
        
        $this->assertEquals('connection_name', $adapter->__getConnectionName());
    }
    
}

class TestableConnectionAdapter extends ConnectionAdapter
{
    public function __getContainer()
    {
        return parent::getContainer();
    }

    public function __getConnectionName()
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
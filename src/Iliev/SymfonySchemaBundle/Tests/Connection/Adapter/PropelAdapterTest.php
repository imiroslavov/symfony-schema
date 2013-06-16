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

use Symfony\Component\DependencyInjection\Container;
use Iliev\SymfonySchemaBundle\Connection\Adapter\PropelAdapter;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class PropelAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropelAdapter;
     */
    protected $adapter;
    
    /**
     * @return \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\MockPropelConfiguration
     */
    public function configuration()
    {
        $data = array(
            'datasources' => array(
                'test1' => array(
                    'adapter' => 'mysql',
                    'connection' => array(
                        'dsn' => 'mysql:host=test_hostname1;dbname=test_database1;charset=UTF8',
                        'user' => 'test_username1',
                        'password' => 'test_password1'
                    )
                ),
                'test2' => array(
                    'adapter' => 'mysql',
                    'connection' => array(
                        'dsn' => 'mysql:host=test_hostname2;port=1337;dbname=test_database2;charset=UTF8',
                        'user' => 'test_username2',
                        'password' => 'test_password2'
                    )
                )
            )
        );

        return new MockPropelConfiguration($data);
    }

    public function setUp()
    {
        if (!class_exists('Propel')) {
            $this->markTestSkipped('Propel not installed');
        }
        
        $configuration = $this->configuration();

        $container = new Container();
        $container->set('propel.configuration', $configuration);

        $this->adapter = new TestablePropelAdapter($container);
    }

    public function tearDown()
    {
        unset($this->adapter);
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\PropelAdapter::initializeParameterBag
     * @group initializeParameterBag
     */
    public function testInitializeParameterBag1()
    {
        $this->adapter->setConnectionName('test1');
        $this->adapter->initializeParameterBag();

        $this->assertEquals('mysql', $this->adapter->getParameterBag()->get('adapter'));
        $this->assertEquals('test_username1', $this->adapter->getParameterBag()->get('username'));
        $this->assertEquals('test_password1', $this->adapter->getParameterBag()->get('password'));
        $this->assertEquals('test_database1', $this->adapter->getParameterBag()->get('database'));
        $this->assertEquals('test_hostname1', $this->adapter->getParameterBag()->get('host'));
        $this->assertNull($this->adapter->getParameterBag()->get('port')); 
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\PropelAdapter::initializeParameterBag
     * @group initializeParameterBag
     */
    public function testInitializeParameterBag2()
    {
        $this->adapter->setConnectionName('test2');
        $this->adapter->initializeParameterBag();

        $this->assertEquals('mysql', $this->adapter->getParameterBag()->get('adapter'));
        $this->assertEquals('test_username2', $this->adapter->getParameterBag()->get('username'));
        $this->assertEquals('test_password2', $this->adapter->getParameterBag()->get('password'));
        $this->assertEquals('test_database2', $this->adapter->getParameterBag()->get('database'));
        $this->assertEquals('test_hostname2', $this->adapter->getParameterBag()->get('host'));
        $this->assertEquals('1337', $this->adapter->getParameterBag()->get('port')); 
    }
    
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\PropelAdapter::parseDsn
     */
    public function testParseDsn()
    {
        $result = $this->adapter->__parseDsn('test_key=test_value;', '/test_key=([a-zA-Z0-9\_]+)/');
        
        $this->assertEquals('test_value', $result);
    }
    
    /**
     * @covers \Iliev\SymfonySchemaBundle\Tests\Connection\Adapter\PropelAdapter::getTemporaryConfiguration
     */
    public function testGetTemporaryConfiguration()
    {
        $this->adapter->setConnectionName('test1');
        $this->adapter->initializeParameterBag();
        $this->adapter->getParameterBag()->set('username', 'test_username3');
        $this->adapter->getParameterBag()->set('password', 'test_password3');
        $this->adapter->getParameterBag()->set('database', 'test_database3');
        $this->adapter->getParameterBag()->set('host', 'test_hostname3');
        $this->adapter->getParameterBag()->set('port', '7331');
        
        $configuration = $this->adapter->__getTemporaryConfiguration();
        
        $this->assertArrayHasKey('datasources', $configuration);
        $this->assertArrayHasKey('test1', $configuration['datasources']);
        $this->assertArrayHasKey('connection', $configuration['datasources']['test1']);
        $this->assertArrayHasKey('dsn', $configuration['datasources']['test1']['connection']);
        $this->assertEquals('mysql:host=test_hostname3;port=7331;dbname=test_database3;charset=UTF8', $configuration['datasources']['test1']['connection']['dsn']);
        $this->assertEquals('test_username3', $configuration['datasources']['test1']['connection']['user']);
        $this->assertEquals('test_password3', $configuration['datasources']['test1']['connection']['password']);
    }
}

class TestablePropelAdapter extends PropelAdapter
{
    public function __parseDsn($dsn, $regex)
    {
        return parent::parseDsn($dsn, $regex);
    }
    
    public function __getTemporaryConfiguration()
    {
        return parent::getTemporaryConfiguration();
    }
}

class MockPropelConfiguration implements \ArrayAccess
{
    protected $parameters; 

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function offsetExists($offset)
    {
    }

    public function offsetGet($offset)
    {
        return $this->parameters[$offset];
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}

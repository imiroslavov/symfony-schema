<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\Tests\ParameterBag;
use Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag::isEmpty
     */
    public function testIsEmpty()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));
        $this->assertFalse($bag->isEmpty('foo'), '->isEmpty() determines whether a variable is empty');
        
        $bag = new ParameterBag(array('foo' => ''));
        $this->assertTrue($bag->isEmpty('foo'));
    }
}

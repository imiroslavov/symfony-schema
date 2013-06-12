<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

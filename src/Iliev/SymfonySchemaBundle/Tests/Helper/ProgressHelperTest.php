<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Iliev\SymfonySchemaBundle\Tests\Helper;

use Symfony\Component\Console\Helper\Helper;
use Iliev\SymfonySchemaBundle\Helper\ProgressHelper;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class ProgressHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $helper = new ProgressHelper();

        $this->assertEquals('progress', $helper->getName());
    }
}

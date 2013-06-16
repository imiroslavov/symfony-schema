<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Iliev\SymfonySchemaBundle\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class ProgressHelper extends Helper
{
    /**
     * Handle all unused method calls
     * 
     * @param string $name
     * @param array $arguments
     * 
     * @codeCoverageIgnore
     */
    public function __call($name, $arguments)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'progress';
    }
}

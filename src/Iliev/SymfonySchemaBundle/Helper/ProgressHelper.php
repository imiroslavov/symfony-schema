<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
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

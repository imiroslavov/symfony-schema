<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\ParameterBag;

use Symfony\Component\HttpFoundation\ParameterBag as BaseParameterBag;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class ParameterBag extends BaseParameterBag
{
    /**
     * @param  string  $path
     * @param  mixed   $default
     * @param  boolean $deep
     * @return boolean
     */
    public function isEmpty($path, $default = null, $deep = false)
    {
        $result = $this->get($path, $default, $deep);
        
        return empty($result);
    }
}

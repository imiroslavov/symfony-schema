<?php

/*
 * This file is part of the SymfonySchema package.
 *
 * (c) Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

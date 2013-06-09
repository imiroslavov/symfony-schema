<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\Connection\Adapter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
abstract class ConnectionAdapter implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ParameterBag
     */
    protected $parameterBag = null;

    /**
     * @var string
     */
    protected $connectionName;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ParameterBag
     */
    public function getParameterBag()
    {
      if (null === $this->parameterBag) {
          $this->parameterBag = new ParameterBag();
      }
      
      return $this->parameterBag;
    }

    /**
     * @return string
     */
    protected function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * @param string $connectionName
     */
    public function initialize($connectionName)
    {
        $this->connectionName = $connectionName;
        
        $this->initializeParameterBag();
    }

    abstract public function initializeParameterBag();
    abstract public function createConnection();
}

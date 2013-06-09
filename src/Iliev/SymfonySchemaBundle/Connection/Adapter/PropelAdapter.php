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

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class PropelAdapter extends ConnectionAdapter
{
    /**
     * @throws \InvalidArgumentException
     */
    public function initializeParameterBag()
    {
        $propelConfiguration = $this->getContainer()->get('propel.configuration');

        if (isset($propelConfiguration['datasources'][$this->getConnectionName()])) {
            $config = $propelConfiguration['datasources'][$this->getConnectionName()];
        } else {
            throw new \InvalidArgumentException(sprintf('Connection named %s doesn\'t exist', $this->getConnectionName()));
        }
        
        $this->getParameterBag()->set('username', $config['connection']['user']);
        $this->getParameterBag()->set('password', $config['connection']['password']);
        $this->getParameterBag()->set('database', $this->parseDsn($config['connection']['dsn'], '/dbname=([a-zA-Z0-9\_]+)/'));
        
        $this->getParameterBag()->set('host', $this->parseDsn($config['connection']['dsn'], '/host=([a-zA-Z0-9\_]+)/'));
        $this->getParameterBag()->set('port', $this->parseDsn($config['connection']['dsn'], '/port=([0-9]+)/'));
    }
    
    /**
     * @return \PropelPDO
     */
    public function createConnection()
    {
        return \Propel::getConnection($this->getConnectionName());
    }

    /**
     * @param  string $dsn
     * @param  string $regex
     * @return mixed
     */
    protected function parseDsn($dsn, $regex)
    {
        $matches = array();
        if (1 === preg_match($regex, $dsn, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

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
     * @var array
     */
    private $configuration;

    /**
     * @throws \InvalidArgumentException
     */
    public function initializeParameterBag()
    {
        $propelConfiguration = $this->getContainer()->get('propel.configuration');

        if (isset($propelConfiguration['datasources'][$this->getConnectionName()])) {
            $this->configuration = $propelConfiguration['datasources'][$this->getConnectionName()];
        } else {
            throw new \InvalidArgumentException(sprintf('Connection named %s doesn\'t exist', $this->getConnectionName()));
        }

        $this->getParameterBag()->set('username', $this->configuration['connection']['user']);
        $this->getParameterBag()->set('password', $this->configuration['connection']['password']);
        $this->getParameterBag()->set('database', $this->parseDsn($this->configuration['connection']['dsn'], '/dbname=([a-zA-Z0-9\_]+)/'));
        
        $this->getParameterBag()->set('host', $this->parseDsn($this->configuration['connection']['dsn'], '/host=([a-zA-Z0-9\_]+)/'));
        $this->getParameterBag()->set('port', $this->parseDsn($this->configuration['connection']['dsn'], '/port=([0-9]+)/'));
        
        $this->getParameterBag()->set('adapter', $this->configuration['adapter']);
        $this->getParameterBag()->set('charset', $this->parseDsn($this->configuration['connection']['dsn'], '/charset=([a-zA-Z0-9\_]+)/'));
    }

    /**
     * @return \PropelPDO
     */
    public function createConnection()
    {
        \Propel::setConfiguration($this->getTemporaryConfiguration());
        
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

    /**
     * @return array
     */
    protected function getTemporaryConfiguration()
    {
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $this->getParameterBag()->get('adapter'),
            $this->getParameterBag()->get('host'),
            $this->getParameterBag()->get('port'),
            $this->getParameterBag()->get('database'),
            $this->getParameterBag()->get('charset')
        );

        $this->configuration['connection']['dsn']      = $dsn;
        $this->configuration['connection']['user']     = $this->getParameterBag()->get('username');
        $this->configuration['connection']['password'] = $this->getParameterBag()->get('password');

        return array(
            'datasources' => array($this->getConnectionName() => $this->configuration)
        );
    }
}

<?php

namespace CryPay;

use CryPay\Services\AbstractService;
use CryPay\Services\PublicService;
use CryPay\Services\ServiceFactory;

/**
 * Client used to send requests to CryPay's API
 */
class Client extends BaseClient
{
    /**
     * @var ServiceFactory
     */
    protected $factory;

    /**
     * @param mixed $apiKey
     * @param bool $useSandboxEnv
     */
    public function __construct($apiKey = null, $useSandboxEnv = false)
    {
        parent::__construct($apiKey, $useSandboxEnv);

        $this->factory = new ServiceFactory($this);
    }

    /**
     * @param string $name
     * @return AbstractService|null
     */
    public function __get($name)
    {
        return $this->factory->__get($name);
    }

    /**
     * @param string $name
     * @param array<int,mixed> $arguments
     * @return PublicService|null
     */
    public function __call($name, $arguments)
    {
        return $this->factory->__call($name, $arguments);
    }
}

<?php

namespace CryPay\Services;

use CryPay\ClientInterface;
use CryPay\Exception\InvalidArgumentException;

/**
 * Abstract base class for all services.
 */
abstract class AbstractService
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Initializes a new instance of the AbstractService class.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Gets the client used by this service to send requests.
     *
     * @return ClientInterface
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Send a request to CryPay API.
     *
     * @param string $method the HTTP method
     * @param string $path   the path of the request
     * @param array<string, mixed> $params the parameters of the request
     * @return mixed
     */
    protected function request( $method,  $path,  $params = [])
    {

        return $this->getClient()->request($method, $path, $params);
    }

    /**
     * Combine base path with parameters
     *
     * @param  string $path
     * @param  mixed  $ids
     * @return string
     */
    protected function buildPath( $path, ...$ids)
    {
        foreach ($ids as $id) {
            if ($id === null || trim($id) === '') {
                throw new InvalidArgumentException('The resource ID cannot be null or whitespace.');
            }
        }

        return vsprintf($path, array_map('urlencode', $ids));
    }
}

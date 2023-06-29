<?php

namespace CryPay\HttpClient;

interface ClientInterface
{
    /**
     * @param string $method
     * @param string $absUrl
     * @param array $headers
     * @param array $params
     * @return mixed
     */
    public function request( $method,  $absUrl,  $headers = [],  $params = []);
}

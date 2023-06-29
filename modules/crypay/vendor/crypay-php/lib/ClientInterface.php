<?php

namespace CryPay;

interface ClientInterface
{
    /**
     * @param string $method
     * @param string $path
     * @param array<string, mixed> $params
     * @return mixed
     */
    public function request($method, $path, $params = []);
}

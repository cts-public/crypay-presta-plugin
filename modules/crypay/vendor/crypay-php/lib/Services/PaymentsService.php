<?php

namespace CryPay\Services;

use CryPay\Resources\CreateOrder;

class PaymentsService extends AbstractService
{
    /**
     * Create order at CryPay and redirect shopper to invoice (shortLink).
     *
     * @param string[] $params
     * @return CreateOrder|mixed
     */
    public function create( $params = [])
    {

        return $this->request('post', '/payments/shortlink', $params);
    }

    /**
     * Verify order at CryPay.
     *
     * @param string[] $params
     * @return mixed
     */
    public function verify( $params = [])
    {
        return $this->request('get', '/payments/verify', $params);
    }

    /**
     * Verify order at CryPay.
     *
     * @param string[] $params
     * @return mixed
     */
    public function options( $params = [])
    {
        return $this->request('get', '/payments/options', $params);
    }

    public function generateSignature() {

    }
}

<?php

namespace CryPay\Exception\Api;

use CryPay\Exception\ApiErrorException;

/**
 * OrderIsNotValid is thrown when order is not valid and HTTP Status: 422 (Unprocessable Entity).
 */
class OrderIsNotValid extends ApiErrorException
{
}

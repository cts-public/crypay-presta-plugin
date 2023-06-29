<?php

namespace CryPay\Exception\Api;

use CryPay\Exception\ApiErrorException;

/**
 * UnprocessableEntity is thrown when HTTP Status: 422 (Unprocessable Entity).
 */
class UnprocessableEntity extends ApiErrorException
{
}

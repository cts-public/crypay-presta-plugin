<?php

namespace CryPay\Exception\Api;

use CryPay\Exception\ApiErrorException;

/**
 * Unauthorized is thrown when HTTP Status: 401 (Unauthorized).
 */
class Unauthorized extends ApiErrorException
{
}

<?php

namespace CryPay\Exception;

use Exception;
use Throwable;

class ApiErrorException extends Exception
{
    /**
     * @var string|null
     */
    protected $reason;

    /**
     * @var string|null
     */
    protected $errors = null;

    /**
     * @var int|null
     */
    protected $httpStatus;

    /**
     * protected constructor
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    final public function __construct($message = "", $code = 0, Throwable $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }

    /**
     * Creates a new API error exception.
     *
     * @param mixed $response
     * @param mixed   $httpStatus
     *
     * @return static
     */
    public static function factory($response, $httpStatus)
    {
        $instance = new static($response['error'] ? $response['error'] : null);
        $instance
            ->setReason($response['error'] ? $response['error'] : null)
            ->setErrorDetails($response['message'] ? $response['message'] : '')
            ->setHttpStatus($httpStatus);

        return $instance;
    }

    /**
     * Gets reason for the error.
     *
     * @return null|string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets reason for the error.
     *
     * @param string|null $reason
     * @return self
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Gets additional error details (if available).
     *
     * @return string[]
     */
    public function getErrorDetails()
    {
        return $this->errors;
    }

    /**
     * Sets additional error details.
     *
     * @param  string|null $errors
     * @return self
     */
    public function setErrorDetails($errors = null)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Gets the HTTP status code.
     *
     * @return null|int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Sets the HTTP status code.
     *
     * @param  int|null $httpStatus
     * @return self
     */
    public function setHttpStatus($httpStatus = null)
    {
        $this->httpStatus = $httpStatus;

        return $this;
    }
}

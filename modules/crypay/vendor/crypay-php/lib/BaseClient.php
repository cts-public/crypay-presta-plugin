<?php

namespace CryPay;

use CryPay\Exception\Api\BadAuthToken;
use CryPay\Exception\Api\BadRequest;
use CryPay\Exception\Api\NotFound;
use CryPay\Exception\Api\OrderIsNotValid;
use CryPay\Exception\Api\OrderNotFound;
use CryPay\Exception\Api\Unauthorized;
use CryPay\Exception\Api\UnprocessableEntity;
use CryPay\Exception\ApiErrorException;
use CryPay\Exception\InvalidArgumentException;
use CryPay\Exception\InternalServerError;
use CryPay\Exception\RateLimitException;
use CryPay\Exception\UnknownApiErrorException;
use CryPay\HttpClient\ClientInterface as HttpClientInterface;
use CryPay\HttpClient\CurlClient;
use CryPay\Services\PaymentsService;
use CryPay\Services\PublicService;
use Exception;

/**
 * Client used to send requests to CryPay's API
 *
 * @property PaymentsService $payment
 * @mixin PublicService
 */
class BaseClient implements ClientInterface
{
    /**
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * @var string default base URL for CryPay's API
     */
    const DEFAULT_API_BASE = 'https://crypay.com/api/v1';

    /**
     * @var string default base URL for CryPay's API
     */
    const SANDBOX_DEFAULT_API_BASE = 'https://dev.crypay.com/api/v1';

    /**
     * @var HttpClientInterface|null
     */
    protected static $httpClient = null;

    /**
     * @var array<string, string>|null
     */
    protected static $appInfo = null;

    /**
     * @var array<string, mixed>
     */
    private $config;

    /**
     * Initializes a new instance of CryPay's BaseClient class.
     *
     * The constructor takes a single argument. The argument can be a string, in which case it
     * should be the API key. It can also be an array with various configuration settings.
     *
     * @param mixed $apiKey
     * @param bool|false $useSandboxEnv
     */
    public function __construct($apiKey = null, $useSandboxEnv = false)
    {
        $config = array_merge(
            $this->getDefaultConfig(),
            [
                'api_key' => $apiKey,
                'environment' => !$useSandboxEnv ? 'live' : 'sandbox'
            ]
        );

        $this->validateConfig($config);

        // check if trying to connect to sandbox environment
        if ($useSandboxEnv) {
            $config['api_base'] = self::SANDBOX_DEFAULT_API_BASE;
        }

        $this->config = $config;
    }

    /**
     * Gets the API key used by the client to send requests.
     *
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->config['api_key'];
    }

    /**
     * @param string|null $apiKey
     * @return $this
     */
    public function setApiKey($apiKey = null)
    {
        $this->config['api_key'] = $apiKey;
        $this->validateConfig($this->config);

        return $this;
    }

    /**
     * Gets the base URL for CryPay's API.
     *
     * @return string
     */
    public function getApiBase()
    {
        return $this->config['api_base'];
    }

    /**
     * Gets the environment used to interact with CryPay's API.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->config['environment'];
    }

    /**
     * @param string $environment
     * @return $this
     */
    public function setEnvironment($environment)
    {
        $this->config['environment'] = $environment;
        $this->validateConfig($this->config);

        return $this;
    }

    public function generateSignature($data, $secret)
    {
        return hash_hmac('sha256', $data, $secret);
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultConfig()
    {
        return [
            'api_key' => null,
            'api_base' => self::DEFAULT_API_BASE,
            'environment' => 'live'
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return void
     */
    private function validateConfig($config = [])
    {
        if ($config['api_key'] !== null) {
            if (!is_string($config['api_key'])) {
                throw new InvalidArgumentException('api_key must be null or a string');
            }

            if (empty($config['api_key'])) {
                throw new InvalidArgumentException('api_key cannot be empty string');
            }

            if (preg_match('/\s/', $config['api_key'])) {
                throw new InvalidArgumentException('api_key cannot contain whitespace');
            }
        }

        // api_base
        if (!is_string($config['api_base'])) {
            throw new InvalidArgumentException('api_base must be a string');
        }

        // environment
        if (!in_array($config['environment'], ['live', 'sandbox'])) {
            throw new InvalidArgumentException('Environment does not exist. Available environments: live, sandbox.');
        }

        // check absence of extra keys
        $extraConfigKeys = array_diff(array_keys($config), array_keys($this->getDefaultConfig()));

        if (!empty($extraConfigKeys)) {
            $invalidKeys = "'" . implode("', '", $extraConfigKeys) . "'";

            throw new InvalidArgumentException('Found unknown key(s) in configuration array: ' . $invalidKeys);
        }
    }

    /**
     * @param string $method
     * @return string[]
     */
    protected function getDefaultHeaders($method)
    {
        $headers = [];

        if (($apiKey = $this->getApiKey()) !== null) {
            $headers[] = 'X-API-KEY: ' . $apiKey;
        }

        if (in_array(strtolower($method), ['post', 'patch'])) {
            $headers[] = 'Content-Type: application/json';
        }

        if (($appInfo = self::getAppInfo()) !== null) {
            $headers[] = 'User-Agent: CryPay (PHP Library v' . self::VERSION . ', '
                . $appInfo['name'] . (!empty($appInfo['version']) ? ' v' . $appInfo['version'] : '') . ')';
        } else {
            $headers[] = 'User-Agent: CryPay (PHP Library v' . self::VERSION . ')';
        }

        return $headers;
    }

    /**
     * Send a request to CryPay API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array $params the parameters of the request
     *
     * @throws ApiErrorException
     */
    public function request($method, $path, $params = [])
    {
        // generate default headers
        $headers = $this->getDefaultHeaders($method);
        // generate absolute url
        $absUrl = $this->getApiBase() . '/' . trim($path, '/');

        $data = $this->getHttpClient()->request($method, $absUrl, $headers, $params);
        $responseBody = $data[0];
        $httpStatus = $data[1];
        $responseFormatted = json_decode($responseBody, true) ?: $responseBody;

        if ($httpStatus !== 200) {
            $this->handleErrorResponse($responseFormatted, $httpStatus);
        }

        return is_array($responseFormatted)
            ? (object)$responseFormatted
            : $responseFormatted;
    }

    /**
     * @throws ApiErrorException
     */

    /**
     * @param mixed $response
     * @param int $httpStatus
     * @return void
     *
     * @throws ApiErrorException
     */
    public function handleErrorResponse($response, $httpStatus)
    {
        $reason = isset($response['error']) ? $response['error'] : null;

        if ($httpStatus === 400) {
            throw BadRequest::factory($response, $httpStatus);
        } elseif ($httpStatus === 403) {
            switch ($reason) {
                case 'ForbiddenError':
                    throw BadAuthToken::factory($response, $httpStatus);

                default:
                    throw Unauthorized::factory($response, $httpStatus);
            }
        } elseif ($httpStatus === 404) {
            switch ($reason) {
                case 'OrderNotFound':
                    throw OrderNotFound::factory($response, $httpStatus);

                default:
                    throw NotFound::factory($response, $httpStatus);
            }
        } elseif ($httpStatus === 422) {
            switch ($reason) {
                case 'OrderIsNotValid':
                    throw OrderIsNotValid::factory($response, $httpStatus);

                case 'OrderNotFound':
                    throw OrderNotFound::factory($response, $httpStatus);

                default:
                    throw UnprocessableEntity::factory($response, $httpStatus);
            }
        } elseif ($httpStatus === 429) {
            throw RateLimitException::factory($response, $httpStatus);
        } elseif (in_array($httpStatus, [500, 504])) {
            throw InternalServerError::factory($response, $httpStatus);
        }

        throw UnknownApiErrorException::factory($response, $httpStatus);
    }

    /**
     * @param HttpClientInterface $httpClient
     * @return void
     */
    public static function setHttpClient(HttpClientInterface $httpClient)
    {
        self::$httpClient = $httpClient;
    }

    /**
     * @return HttpClientInterface
     */
    protected function getHttpClient()
    {
        if (!self::$httpClient) {
            self::$httpClient = CurlClient::instance();
        }

        return self::$httpClient;
    }

    /**
     * @return array<string, string>|null The application's information
     */
    public static function getAppInfo()
    {
        return self::$appInfo;
    }

    /**
     * @param string $appName The application's name
     * @param null|string $appVersion The application's version
     */
    public static function setAppInfo($appName, $appVersion = null)
    {
        self::$appInfo = [];
        self::$appInfo['name'] = trim($appName);
        self::$appInfo['version'] = trim($appVersion);
    }
}

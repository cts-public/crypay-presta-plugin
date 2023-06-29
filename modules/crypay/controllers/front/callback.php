<?php
/**
 * @author    CryPay <info@crypay.com>
 * @copyright 2023 CryPay
 * @license   https://www.opensource.org/licenses/MIT  MIT License
 */

require_once(_PS_MODULE_DIR_ . '/crypay/vendor/crypay-php/init.php');

class CrypayCallbackModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /** @var array */
    protected $requestData;

    protected $request;

    public function postProcess()
    {
        parent::postProcess();
        try {
            $this->request = Tools::file_get_contents('php://input');
            $this->logInfo('CryPay reportPayload: ' . $this->request);
            $headers = $this->get_ds_headers();
            if (!array_key_exists("XSignature", $headers)) {
                $error_message = 'CryPay X-SIGNATURE: not found';
                $this->logError($error_message);
                throw new Exception($error_message, 400);
            }

            $signature = $headers["XSignature"];

            $this->requestData = json_decode($this->request, true);
            if (false === $this->checkIfRequestIsValid()) {
                $error_message = 'CryPay Request: not valid request data';
                $this->logError($error_message);
                throw new Exception($error_message, 400);
            }

            if ($this->requestData['type'] !== 'PAYMENT') {
                $error_message = 'CryPay Request: not valid request type';
                $this->logError($error_message);
                throw new Exception($error_message, 400);
            }

            $cart_id = (int)$this->requestData['variableSymbol'];
            $order_id = Order::getOrderByCartId($cart_id);
            $order = new Order($order_id);
            $currency = new Currency($order->id_currency);


            if (!$cart_id) {
                $error_message = 'Shop order #' . $this->requestData['variableSymbol'] . ' does not exists';
                $this->logError($error_message, $cart_id);
                throw new Exception($error_message, 400);
            }

            if ($currency->iso_code != $this->requestData['currency']) {
                $error_message = 'CryPay Currency: ' . $this->requestData['currency'] . ' is not valid';
                $this->logError($error_message, $cart_id);

                throw new Exception($error_message, 400);
            }

            $apiKey = Configuration::get('CRYPAY_API_KEY');
            $environment = (Configuration::get('CRYPAY_TEST')) == 1;
            $client = new \CryPay\Client($apiKey, $environment);

            $token = $client->generateSignature($this->request, Configuration::get('CRYPAY_API_SECRET'));

            if (empty($signature) || strcmp($signature, $token) !== 0) {
                $error_message = 'CryPay X-SIGNATURE: ' . $signature . ' is not valid. valid X-SIGNATURE ' . $token;
                $this->logError($error_message, $cart_id);
                throw new Exception($error_message, 400);
            }

            switch ($this->requestData['state']) {
                case 'SUCCESS':
                    if (((float)$order->getOrdersTotalPaid()) == ((float)$this->requestData['amount'])) {
                        $order_status = 'PS_OS_PAYMENT';
                        break;
                    } else {
                        $order_status = 'CRYPAY_INVALID';
                        $this->logError('PS Orders Total does not match with Crypay Price Amount', $cart_id);
                    }
                    break;
                case 'WAITING_FOR_PAYMENT':
                    $order_status = 'CRYPAY_PENDING';
                    break;
                case 'WAITING_FOR_CONFIRMATION':
                    $order_status = 'CRYPAY_CONFIRMING';
                    break;
                case 'EXPIRED':
                    $order_status = 'CRYPAY_EXPIRED';
                    break;
                default:
                    $order_status = false;
            }

            if ($order_status && Configuration::get($order_status) != $order->current_state && $order->current_state != Configuration::get('PS_OS_PAYMENT')) {
                $history = new OrderHistory();
                $history->id_order = $order->id;
                $history->changeIdOrderState((int)Configuration::get($order_status), $order->id);
                $history->addWithemail(true, array(
                    'order_name' => $cart_id,
                ));

                $this->response('OK');

            } else {
                $this->response('Order Status ' . $this->requestData['state'] . ' not implemented');
            }

        } catch (Exception $e) {
            $this->response($e->getMessage(), $e->getCode());
        }

        if (_PS_VERSION_ >= '1.7') {
            $this->setTemplate('module:crypay/views/templates/front/payment_callback.tpl');
        } else {
            $this->setTemplate('payment_callback.tpl');
        }
    }

    private function checkIfRequestIsValid()
    {
        return true;
//        return true === Validate::isString($this->requestData['type'])
//            && true === Validate::isString($this->requestData['state'])
//            && true === Validate::isUnsignedFloat($this->requestData['amount'])
//            && true === Validate::isString($this->requestData['currency'])
//            && true === Validate::isString($this->requestData['variableSymbol']);
    }

    function get_ds_headers()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    private function logInfo($message, $cart_id = null)
    {
        PrestaShopLogger::addLog($message, 1, null, 'Cart', $cart_id, true);
    }

     private function logError($message, $cart_id = null)
    {
        PrestaShopLogger::addLog($message, 3, null, 'Cart', $cart_id, true);
    }

    private function response($message, $status = 200)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        if ($status === 200) {
            echo json_encode(['status' => 'success', 'message' => $message]);
        } else {
            echo json_encode(['status' => 'error', 'error' => $message]);
        }

        die();
    }
}

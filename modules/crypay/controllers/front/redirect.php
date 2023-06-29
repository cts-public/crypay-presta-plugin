<?php
/**
 * @author    CryPay <info@crypay.com>
 * @copyright 2023 CryPay
 * @license   https://www.opensource.org/licenses/MIT  MIT License
 */

require_once(_PS_MODULE_DIR_ . '/crypay/vendor/crypay-php/init.php');

class CrypayRedirectModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $id_order = Tools::getValue('id_order');

        if ($id_order) {
            $key = Tools::getValue('key');
            $cart = Cart::getCartByOrderId($id_order);
            if (_PS_VERSION_ < '1.7') {
                $order = new Order((int)$id_order);
            } else {
                $order = Order::getByCartId((int)$cart->id);
            }
            $customer = new Customer((int)$order->id_customer);
            if ($key != $customer->secure_key) {
                die('Access denied for this operation');
                Tools::redirect('index.php');
            }
        } else {
            $cart = $this->context->cart;
        }

        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        $total = (float)number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', '');
        $currency = Context::getContext()->currency;

        $apiKey = Configuration::get('CRYPAY_API_KEY');
        $environment = (Configuration::get('CRYPAY_TEST')) == 1;

        $client = new \CryPay\Client($apiKey, $environment);
        $client::setAppInfo("PrestashopMarketplace", $this->module->version);

        $customer = new Customer($cart->id_customer);


        if(!$id_order) {
            $id_order = $this->module->currentOrder;
        }


        $success_url = $this->context->link->getModuleLink('crypay', 'success', [
            'cart_id' => $cart->id,
            'key' => $customer->secure_key
        ]);

        $fail = $this->context->link->getModuleLink('crypay', 'cancel', [
            'cart_id' => $cart->id,
            'key' => $customer->secure_key
        ]);

        $params = [
            "symbol" => $currency->iso_code,
            "amount" => $total,
            "currency" => $currency->iso_code,
            "variableSymbol" => (string)$cart->id,
            'successUrl' => $success_url,
            'failUrl' => $fail,
        ];

        if ((Configuration::get('CRYPAY_TEST')) == 1) {
            $this->logInfo('send redirect params ' . json_encode($params));
        }
        try {
            $orderUrl = $client->payment->create($params);
        } catch (\Exception $e) {
            $this->logError($e->getMessage(), $cart->id);
            Tools::redirect('index.php?controller=order&step=3');
        }
        if (!isset($orderUrl) || !isset($orderUrl->shortLink) || !$orderUrl->shortLink) {
            Tools::redirect('index.php?controller=order&step=3');
        }

        if (!$id_order) {
            $this->module->validateOrder(
                (int)$cart->id,
                Configuration::get('CRYPAY_PENDING'),
                $total,
                $this->module->displayName,
                null,
                null,
                (int)$currency->id,
                false,
                $customer->secure_key
            );
        }

        Tools::redirect($orderUrl->shortLink);
    }

    private function logInfo($message, $cart_id = 0)
    {
        PrestaShopLogger::addLog($message, 1, null, 'Cart', $cart_id, true);
    }

    private function logError($message, $cart_id = 0)
    {
        PrestaShopLogger::addLog('[create crypay order] '.$message, 3, null, 'Cart', $cart_id, true);
    }
}

<?php
/**
 * @author    CryPay <info@crypay.com>
 * @copyright 2023 CryPay
 * @license   https://www.opensource.org/licenses/MIT  MIT License
 */

class CrypayCancelModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $key = Tools::getValue('key');
        $cart_id = Tools::getValue('cart_id');
        $order_id = Order::getOrderByCartId($cart_id);
        $order = new Order($order_id);
        $customer = new Customer((int)$order->id_customer);
        $currency = new Currency($order->id_currency);

        if ($key != $customer->secure_key) {
            die('Access denied for this operation');
            Tools::redirect('index.php');
        }

        if ($order->module != $this->module->name) {
            die('Access denied for this operation');
            Tools::redirect('index.php');
        }

        $url_confirmation = $this->context->link->getPageLink(
            'order-confirmation',
            true,
            null,
            array(
                'key' => $customer->secure_key,
                'id_cart' => (int)$order->id_cart,
                'id_module' => (int)$this->module->id,
                'id_order' => $order->id,
            )
        );

     //   Tools::redirectLink($url_confirmation . '&crypay_error=1');
        Tools::redirectLink($url_confirmation);
    }
}

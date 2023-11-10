<?php
/**
 * @author    CryPay <info@crypay.com>
 * @copyright 2023 CryPay
 * @license   https://www.opensource.org/licenses/MIT  MIT License
 */

class CrypaySuccessModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $key = Tools::getValue('key');
        $id_order = Tools::getValue('id_order');
        $order = new Order($id_order);
        
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

        if (_PS_VERSION_ < '1.7') {
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
        } else {

            $this->context->smarty->assign(array(
                'crypay_production' => (Configuration::get('CRYPAY_TEST')) == 0,
                'crypay_id_order' => $order->id,
                'crypay_reference_order' => $order->reference,
                'crypay_total_to_pay' => Tools::displayPrice($order->total_paid, $currency, false),
            ));


            $this->setTemplate('module:crypay/views/templates/front/crypay_payment_success.tpl');
        }
    }
}

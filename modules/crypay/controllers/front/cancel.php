<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2015 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * CrypayCancelModuleFrontController
 */
class CrypayCancelModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $key = Tools::getValue('key');
        $id_order = Tools::getValue('id_order');
        $order = new Order($id_order);
        $customer = new Customer((int) $order->id_customer);
        $currency = new Currency($order->id_currency);

        if ($key != $customer->secure_key) {
            echo 'Access denied for this operation';
            exit;
        }

        if ($order->module != $this->module->name) {
            echo 'Access denied for this operation';
            exit;
        }

        $url_confirmation = $this->context->link->getPageLink(
            'order-confirmation',
            true,
            null,
            [
                'key' => $customer->secure_key,
                'id_cart' => (int) $order->id_cart,
                'id_module' => (int) $this->module->id,
                'id_order' => $order->id,
            ]
        );

        Tools::redirectLink($url_confirmation);
    }
}

{**
* Copyright since 2007 PrestaShop SA and Contributors
* PrestaShop is an International Registered Trademark & Property of PrestaShop SA
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.md.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* @author    PrestaShop SA and Contributors <contact@prestashop.com>
* @copyright Since 2007 PrestaShop SA and Contributors
* @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="box">
    <h3 class="page-subheading">{l s='payment information on the payment gateway' mod='crypay'}</h3>

    {if !$crypay_production}
        <p>
            <span style="color: red;">{l s='Test mode, payments do not actually take place.' mod='crypay'}</span>
        </p>
    {/if}

    <p>
        {l s='The payment has not yet been processed, below you can pay the order by cash on delivery.' mod='crypay'}
    </p>

    <table class="table" cellspacing="0">
        <tr>
            <td><strong>{l s='Order number' mod='crypay'}</strong></td>
            <td>{$crypay_id_order|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td><strong>{l s='Order reference' mod='crypay'}</strong></td>
            <td>{$crypay_reference_order|escape:'htmlall':'UTF-8'}</td>
        </tr>
        <tr>
            <td><strong>{l s='Amount to be paid' mod='crypay'}</strong></td>
            <td>{$crypay_total_to_pay|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </table>

    <a class="btn btn-primary" href="{$crypay_url_payment|escape:'htmlall':'UTF-8'}">{l s='Pay with cryptocurrency' mod='crypay'}</a>

    <div style="clear: both;"></div>

</div>
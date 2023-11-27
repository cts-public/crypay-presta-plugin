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
{extends 'customer/page.tpl'}

{block name='page_content'}

    <div class="box" style="background: #ffffff; border: 1px solid #e2e2e2; padding: 30px;">

        <h2 class="page-subheading">
            {l s='Your order has been successfully paid.' mod='crypay'}
        </h2>

        <div class="row">
            <div class="col-xs-12 col-sm-12">

                {if !$crypay_production}
                    <p>
                        <span style="color: red;">{l s='Test mode, payments do not actually take place.' mod='crypay'}</span>
                    </p>
                {/if}

                <p>
                    {l s='Thank you for your payment.' mod='crypay'}
                    {l s='We have sent you a confirmation of payment to your email.' mod='crypay'}
                    {l s='Your order will be processed as soon as possible.' mod='crypay'}
                </p>

                <table class="table" cellspacing="0">
                    <tr>
                        <td><strong>{l s='Order number' mod='crypay'}</strong></td>
                        <td>{$crypay_id_order}</td>
                    </tr>
                    <tr>
                        <td><strong>{l s='Order reference' mod='crypay'}</strong></td>
                        <td>{$crypay_reference_order}</td>
                    </tr>
                    <tr>
                        <td><strong>{l s='Order amount' mod='crypay'}</strong></td>
                        <td>{$crypay_total_to_pay}</td>
                    </tr>
                </table>

                <p>
                    {l s='If you have any questions or need more information, please contact us at our' mod='crypay'} <strong class="dark"><a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='crypay'}</a>.</strong>
                </p>

            </div>
        </div>

    </div>

{/block}

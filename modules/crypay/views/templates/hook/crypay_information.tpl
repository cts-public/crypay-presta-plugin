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
<div class="tab">
    <button class="tablinks" onclick="changeTab(event, 'Information')" id="defaultOpen">{l s='Information' mod='crypay'}</button>
    <button class="tablinks" onclick="changeTab(event, 'Configure Settings')">{l s='Configure Settings' mod='crypay'}</button>
</div>

<!-- Tab content -->
<div id="Information" class="tabcontent">
    <div class="wrapper">
        <img src="../modules/crypay/views/img/invoice.png" style="float:right;"/>
        <h2 class="crypay-information-header">
            {l s='Accept Bitcoin, Litecoin, Ethereum and other digital currencies on your PrestaShop store with CryPay' mod='crypay'}
        </h2><br/>
        <strong>{l s='What is CryPay?' mod='crypay'}</strong> <br/>
        <p>
            {l s='We offer a fully automated cryptocurrency processing platform and invoice system. Accept any cryptocurrency and get paid in Euros or
       U.S. Dollars directly to your bank account (for verified merchants), or just keep bitcoins!' mod='crypay'}
        </p><br/>
        <strong>{l s='Getting started' mod='crypay'}</strong><br/>
        <p>
        <ul>
            <li>{l s='Install the CryPay module on PrestaShop' mod='crypay'}</li>
            <li>
                {l s='Visit ' mod='crypay'}<a href="https://crypay.com" target="_blank">{l s='crypay.com' mod='crypay'}</a>
                {l s='and create an account' mod='crypay'}
            </li>
        </ul>
        </p>
        <p class="sign-up"><br/>
            <a href="https://crypay.com/sign_up" class="sign-up-button">{l s='Sign up on CryPay' mod='crypay'}</a>
        </p><br/>
        <strong>{l s='Features' mod='crypay'}</strong>
        <p>
        <ul>
            <li>{l s='The gateway is fully automatic - set and forget it.' mod='crypay'}</li>
            <li>{l s='Payment amount is calculated using real-time exchange rates' mod='crypay'}</li>
            <li>{l s='Your customers can select to pay with Bitcoin, Litecoin, Ethereum and other cryptocurrencies at checkout, while your payouts are in single currency of your choice.' mod='crypay'}</li>
            <li>
                <a href="https://dev.crypay.com" target="_blank">
                    {l s='Sandbox environment' mod='crypay'}
                </a> {l s='for testing with Testnet Bitcoin.' mod='crypay'}
            </li>
            <li>{l s='Transparent pricing: no setup or recurring fees.' mod='crypay'}</li>
            <li>{l s='No chargebacks - guaranteed!' mod='crypay'}</li>
        </ul>
        </p>

        <p><i>{l s='Questions? Contact support@crypay.com !' mod='crypay'}</i></p>
    </div>
</div>

<div id="Configure Settings" class="tabcontent">
    {html_entity_decode($form|escape:'htmlall':'UTF-8')}
</div>

<script>
    document.getElementById("defaultOpen").click();
</script>

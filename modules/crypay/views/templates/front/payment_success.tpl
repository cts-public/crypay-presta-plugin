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

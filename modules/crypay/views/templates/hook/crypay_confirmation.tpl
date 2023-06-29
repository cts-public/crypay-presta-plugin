<div class="box">
    <div class="row">
        <div class="col-xs-12 col-sm-12">

            <h3 class="page-subheading">
                {l s='Your payment status' mod='crypay'}
            </h3>

            {if !$crypay_production}
                <p>
                    <span style="color: red;">{l s='Test mode, payments do not actually take place.' mod='crypay'}</span>
                </p>
            {/if}

{*            {if !$crypay_status_payment}*}

                <div class="alert alert-danger" role="alert">
                    <i class="material-icons rtl-no-flip">error_outline</i>
                    {l s='We do not have information about the successful payment of the payment. You can repeat the payment using the link below.' mod='crypay'}
                </div>

                <p>
                    <a class="btn btn-primary" href="{$crypay_repeat_payment_url}">{l s='Pay with cryptocurrency' mod='crypay'}</a>
                </p>

{*            {else}*}

{*                <p>*}
{*                    <a class="btn btn-primary" href="{$crypay_check_url}">{l s='Check payment' mod='crypay'}</a>*}
{*                </p>*}

{*            {/if}*}

            <p>
                {l s='If you have any questions or need more information, please contact us at our' mod='crypay'} <strong class="dark"><a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='crypay'}</a>.</strong>
            </p>



        </div>
    </div>
</div>

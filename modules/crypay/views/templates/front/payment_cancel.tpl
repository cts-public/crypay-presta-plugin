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
            <td>{$crypay_id_order}</td>
        </tr>
        <tr>
            <td><strong>{l s='Order reference' mod='crypay'}</strong></td>
            <td>{$crypay_reference_order}</td>
        </tr>
        <tr>
            <td><strong>{l s='Amount to be paid' mod='crypay'}</strong></td>
            <td>{$crypay_total_to_pay}</td>
        </tr>
    </table>

    <a class="btn btn-primary" href="{$crypay_url_payment}">{l s='Pay with cryptocurrency' mod='crypay'}</a>

    <div style="clear: both;"></div>

</div>
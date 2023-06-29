<div class="row hidden-print">

	<div class="col-lg-6">
		<div class="panel">

			<div class="panel-heading">
				<i class="icon-money"></i>
					{l s='Informace k platební bráně GoPay' mod='crypay'}
			</div>

			{if $crypay_payment}

				<div class="row">
					<label class="control-label col-lg-4" style="text-align: right;">
						<strong>{l s='Informace k platbě: ' mod='crypay'}</strong>
					</label>
					<div class="col-lg-8">
						<ul>
					        <li><b>{l s='Stav: ' mod='crypay'}</b> <span class="badge {if $crypay_payment.state == 'PAID'}badge-success{else}badge-warning{/if}">{$crypay_payment.state}</span></li>
					        <li><b>{l s='ID platby GoPay: ' mod='crypay'}</b> {$crypay_payment.id_gopay}</li>
				        </ul>
					</div>
				</div>

				<br />

			{/if}

			<div class="row">
				<label class="control-label col-lg-4" style="text-align: right;">
					<strong>{l s='Komunikace s GoPay platební bránou: ' mod='crypay'}</strong>
				</label>
				<div class="col-lg-8">
					<ul>
						{if $crypay_info}

							{foreach from=$crypay_info item=item_info}
						        <li>{$item_info.date_add} : <b>{$item_info.type}</b> - {$item_info.message}</li>
						    {/foreach}

						{else}

							<li>{l s='Nenalezena žádná komunikace s platební bránou GoPay.' mod='crypay'}

					    {/if}

			        </ul>
				</div>
			</div>

			<br />

			{if $crypay_payment.state != 'PAID' && $crypay_payment.state != 'PARTIALLY_REFUNDED' && $crypay_payment.state != 'REFUNDED'}

			    <div class="row">
					<label class="control-label col-lg-4" style="text-align: right;">
						<strong>{l s='Odkaz na platbu: ' mod='crypay'}</strong>
					</label>
					<div style="overflow-wrap: break-word; word-wrap: break-word; hyphens: auto;" class="col-lg-8">
						<ul>
					        <li><a href="{$crypay_payment_link}" target="_blank">{$crypay_payment_link}</a></li>
				        </ul>
					</div>
				</div>

				<br />

			{/if}

			{if $crypay_mail_allowed && ($crypay_payment.state != 'PAID' && $crypay_payment.state != 'PARTIALLY_REFUNDED' && $crypay_payment.state != 'REFUNDED')}

				<form action="" method="post" id="crypay_send">
			        <input type="hidden" class="hidden" name="SubmitGopayLinkToCustomer" value="1" />
			        <input type="hidden" class="hidden" name="crypay_id_order" value="{$crypay_id_order}" />
			        <button class="button btn btn-default button-medium" type="submit" id="GopaySendLink" name="GopaySendLink">
			            <span>
			            	<i class="icon-envelope left"></i>
			                {l s='Odeslat odkaz k platbě zákazníkovi na email' mod='crypay'}
			        	</span>
			    	</button>
			    </form>

			    <br />

			{/if}

		</div>
	</div>

	{if $crypay_refund_data}
		<div class="col-lg-6">
			<div class="panel">

				<div class="panel-heading">
					<i class="icon-money"></i>
						{l s='Refundace platby přes GoPay' mod='crypay'}
				</div>

				<div class="well hidden-print clearfix">
			        <form name="crypay_refund" id="crypay_refund" action="" method="post">
						<div class="form-horizontal">

							<input type="hidden" class="hidden" name="SubmitDmGopayRefund" value="1" />
							<input type="hidden" class="hidden" name="Submit_crypay_total_pay" value="{$crypay_refund_data.total}" />
							<input type="hidden" class="hidden" name="crypay_id_order" value="{$crypay_id_order}" />
							<input type="hidden" class="hidden" name="crypay_id_currency" value="{$crypay_id_currency}" />

				            <div class="form-group">
				                <label class="control-label col-lg-3">{l s='Zaplacená částka' mod='crypay'}</label>
				                    <div class="input-group col-lg-4">
				                        
				                        <input id="crypay_total_pay" type="text" name="crypay_total_pay" value="{Tools::ps_round($crypay_refund_data.total, $crypay_decimals)}" disabled>
				                        <span class="input-group-addon">
				                            {$crypay_currency_sign}
				                        </span>
				                    </div>
				            </div>

				            <div class="form-group">
				                <label class="control-label col-lg-3">{l s='Částka k refundaci' mod='crypay'}</label>
				                    <div class="input-group col-lg-4">
				                        <input id="crypay_amount_to_refund" type="text" name="crypay_amount_to_refund" value="0">
				                        <span class="input-group-addon">
				                            {$crypay_currency_sign}
				                        </span>
				                    </div>
				            </div>

				            <button type="submit" id="DmGopayRefund" class="btn btn-primary pull-right" name="DmGopayRefund">
				                {l s='Vrátit částku' mod='crypay'}
				            </button>

				            <br />
				            <br />
				            <br />

				            <i>
				            {l s='* Dbejte pravidel pro vratku peněz zákazníkům.' mod='crypay'}
				            {l s='Obvykle lze provést u většiny platebních metod refundaci až po 24 hodinách od provedené platby.' mod='crypay'}
				            {l s='V některých případech je potřeba mít službu povolenou na platební bráně.' mod='crypay'}
				            </i>

				            <br />
				            <br />

				            <i>
				            {l s='** Po odeslání požadavku na vrácení peněz vyčkejte do potvrzení.' mod='crypay'}
				            {l s='Proces obvykle trvá cca 10 vteřin.' mod='crypay'}
				            </i>


						</div>
			        </form>
			    </div>

			</div>
		</div>
	{/if}

</div>

{literal}
	<script type="text/javascript">
		$(document).ready(function () {

		  	$("form#crypay_refund").submit(function(e){

		        $(this).find("button").attr('disabled', 'disabled');

		    });

		});
	</script>
{/literal}


<h3>{l s='Pay with BitPagos' mod='bitpagos'}</h3>

<form action='{$form_action}'>
	<div style="text-align: center">
		<p>{l s='Thank you for your order, please click the button below to pay with BitPagos'}</p>
		<script src='https://www.bitpagos.net/public/js/partner/m.js' 
				class='bp-partner-button' 
				data-role='checkout' 
				data-account-id='{$account_id}' 
				data-reference-id='{$reference_id}' 
				data-title='{$title}' 
				data-amount='{$amount}' 
				data-currency='{$currency}' 
				data-description='{$description}' 
				data-ipn='{$ipn_url}'>
		</script>
	</div>
</form>
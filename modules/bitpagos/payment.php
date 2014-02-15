<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/bitpagos.php');

if ( !$cookie->isLogged() ) {
    Tools::redirect('authentication.php?back=order.php');
}
	
$bitpagos = new bitpagos();
if ( $bitpagos->is_configured() ) {
	$order = $bitpagos->createPendingOrder( $cart );
	if ( $order ) {
		echo $bitpagos->showBitPagosButton( $cart, $order );
	} else {
		die( 'Order not created' );
	}
} else {
	die( 'BitPagos not configured' );
}

include_once(dirname(__FILE__).'/../../footer.php');

?>
<?php

class bitpagosbitpagosModuleFrontController extends FrontController
{
	
	public $php_self = 'bitpagos';

	public function __construct() {
		$this->getIpnResponse();
	}

	public function run() {}


	private function getIpnResponse() {

		$transaction_id = Tools::safeOutput( Tools::getValue('transaction_id') );
		$reference_id = Tools::safeOutput( Tools::getValue('reference_id') );
		$api_key = Configuration::get('BITPAGOS_API_KEY');

		if (empty( $transaction_id ) || 
			empty( $reference_id ) ) {
			header("HTTP/1.1 500 BAD_PARAMETERS");
			return false;
		}

		$url = 'https://www.bitpagos.net/api/v1/transaction/' . $transaction_id . '/?api_key=' . $api_key . '&format=json';

		$cbp = curl_init( $url );
		curl_setopt($cbp, CURLOPT_RETURNTRANSFER, TRUE);
		$response_curl = curl_exec( $cbp );
		curl_close( $cbp );
		$response = json_decode( $response_curl );
		if ( $reference_id != $response->reference_id ) {
			die('Wrong reference id');
		}

		if ( $response->status == 'PA' || $response->status == 'CO' ) {

			$order = new Order( $reference_id );
			$completed = Configuration::get('BITPAGOS_COMPLETED');
			$order->setCurrentState( $completed );

    	}

	}

}

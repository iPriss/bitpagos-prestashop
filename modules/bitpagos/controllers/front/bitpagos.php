<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class bitpagosbitpagosModuleFrontController extends FrontController
{

    public $php_self = 'bitpagos';

    public function __construct()
    {
        $this->getIpnResponse();
    }

    public function run()
    {

    }

    private function getIpnResponse()
    {

        $transaction_id = Tools::safeOutput(Tools::getValue('transaction_id'));
        $reference_id = Tools::safeOutput(Tools::getValue('reference_id'));
        $api_key = Configuration::get('BITPAGOS_API_KEY');

        if (empty($transaction_id) ||
            empty($reference_id)) {
            header("HTTP/1.1 500 BAD_PARAMETERS");
            return false;
        }

        $url = 'https://www.bitpagos.net/api/v1/transaction/';
        $url .= $transaction_id . '/?api_key=' . $api_key . '&format=json';

        $cbp = curl_init($url);
        curl_setopt($cbp, CURLOPT_RETURNTRANSFER, true);
        $response_curl = curl_exec($cbp);
        curl_close($cbp);
        $response = Tools::jsonDecode($response_curl);
        if ($reference_id != $response->reference_id) {
            die('Wrong reference id');
        }

        if ($response->status == 'PA' || $response->status == 'CO') {

            $order = new Order($reference_id);
            $completed = Configuration::get('BITPAGOS_COMPLETED');
            $order->setCurrentState($completed);

        }

    }

}

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

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_MODULE_DIR_.'bitpagos/bitpagos.php');

/*
 * Instant payment notification class.
 * (wait for BitPagos payment confirmation, then validate order)
 */

// Si no es JSON y mandan todo por post separado
$jsonInput = Tools::file_get_contents('php://input');

if (Tools::getIsset('transaction_id')) {
    $dataInput = array();
    $dataInput['date_created']   = Tools::getValue('date_created');
    $dataInput['resource_uri']   = Tools::getValue('resource_uri');
    $dataInput['resource']       = Tools::getValue('resource');
    $dataInput['transaction_id'] = Tools::getValue('transaction_id');
    $dataInput['reference_id']   = Tools::getValue('reference_id');

    $ipn = new BitPagos();
    $ipn->confirmOrder($dataInput);
} elseif ($jsonInput) {
    $ipn = new BitPagos();
    $dataInput = Tools::jsonDecode($jsonInput, true);
    $ipn->confirmOrder($dataInput);
}

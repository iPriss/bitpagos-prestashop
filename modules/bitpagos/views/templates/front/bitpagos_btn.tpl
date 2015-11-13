{*
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
*}

<h3>{l s='Pay with BitPagos' mod='bitpagos'}</h3>

<form action="{$form_action|escape:'htmlall':'UTF-8'}">
    <div style="text-align: center">
        <p>{l s='Thank you for your order, please click the button below to pay with BitPagos' mod='bitpagos'}</p>
        <script src='https://www.bitpagos.net/public/js/partner/m.js'
                class='bp-partner-button'
                data-role='checkout'
                data-account-id="{$account_id|escape:'htmlall':'UTF-8'}"
                data-reference-id="{$reference_id|escape:'htmlall':'UTF-8'}"
                data-title="{$title|escape:'htmlall':'UTF-8'}"
                data-amount="{$amount|escape:'htmlall':'UTF-8'}"
                data-currency="{$currency|escape:'htmlall':'UTF-8'}"
                data-description="{$description|escape:'htmlall':'UTF-8'}"
                data-ipn="{$ipn_url|escape:'htmlall':'UTF-8'}">
        </script>
    </div>
</form>
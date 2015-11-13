<?php
/**
* 2007-20º5 PrestaShop
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

class BitPagos extends PaymentModule
{

    private $html = '';
    private $postErrors = array();

    public function __construct()
    {

        $this->name = 'bitpagos';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'BitPagos';
        $this->controllers = array('payment', 'validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        // The parent construct is required for translations
        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('BitPagos');
        $this->description = $this->l('BitPagos Payment module');

    }

    public function install()
    {

        if (!parent::install()
            or !$this->registerHook('payment')
            or !$this->registerHook('paymentReturn')) {
            return false;
        }

        return $this->createBitPagosStates();

    }

    private function createBitPagosStates()
    {

        $states = array('BITPAGOS_PENDING' => 'BitPagos: Pending', 'BITPAGOS_COMPLETED' => 'BitPagos: Completed');

        $languages = Language::getLanguages();

        foreach ($states as $key => $state) {
            $orderState = new OrderState();
            $orderState->name = array();

            foreach ($languages as $language) {
                $orderState->name[$language['id_lang']] = $state;
            }

            $orderState->color = '#000000';
            $orderState->send_email = false;
            $orderState->hidden = false;
            $orderState->module = $this->name;
            $orderState->delivery = false;
            $orderState->logable = false;

            if ($orderState->add()) {
                Configuration::updateValue($key, $orderState->id);
            } else {
                return false;
            }

        }

        return true;

    }

    public function uninstall()
    {

        $orderState = new OrderState((int)Configuration::get('BITPAGOS_PENDING'));
        $orderState->delete();
        $orderState = new OrderState((int)Configuration::get('BITPAGOS_COMPLETED'));
        $orderState->delete();

        Configuration::deleteByName('BITPAGOS_PENDING');
        Configuration::deleteByName('BITPAGOS_COMPLETED');

        if (!parent::uninstall()) {
            return false;
        }

        return true;

    }

    public function isConfigured()
    {

        $account_id = Tools::safeOutput(Configuration::get('BITPAGOS_ACCOUNT_ID'));
        $api_key = Tools::safeOutput(Configuration::get('BITPAGOS_API_KEY'));
        if (empty($account_id) || empty($api_key)) {
            return false;
        }

        return true;

    }

    // Get admin form
    public function getContent()
    {

        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {

            if ($this->validateSettings()) {
                Configuration::updateValue('BITPAGOS_ACCOUNT_ID', Tools::safeOutput(Tools::getValue('account_id')));
                Configuration::updateValue('BITPAGOS_API_KEY', Tools::safeOutput(Tools::getValue('api_key')));
            } else {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            }

            return true;

        }

        return $output . $this->displayForm();

    }

    private function validateSettings()
    {

        $account_id = Tools::safeOutput(Tools::getValue('account_id'));
        $api_key = Tools::safeOutput(Tools::getValue('api_key'));
        if (!empty($account_id) && !empty($api_key)) {
            return true;
        }

        return false;

    }

    // Admin form
    private function displayForm()
    {

        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $account_id = (Tools::getValue('account_id') ?
                       Tools::getValue('account_id') :
                       Configuration::get('BITPAGOS_ACCOUNT_ID'));
        $api_key = (Tools::getValue('api_key') ? Tools::getValue('api_key') : Configuration::get('BITPAGOS_API_KEY'));

        // Init Fields form array
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings BitPagos API'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Api Key'),
                    'name' => 'api_key',
                    'size' => 40,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Account ID'),
                    'name' => 'account_id',
                    'size' => 40,
                    'required' => true
                ),
                /*
                array(
                    'type' => 'label',
                    'label' => $this->l('IPN URL'),
                    'name' => 'ipn_url',
                    'size' => 60,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default Order Status'),
                    'name' => 'default_order_status',
                    'options' => array(
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                )
                */
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Load current value
        $helper->fields_value['api_key'] = $api_key;
        $helper->fields_value['account_id'] = $account_id;

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        return $helper->generateForm($fields_form);

    }

    public function createPendingOrder($cart)
    {

        $pending_status = Configuration::get('BITPAGOS_PENDING');
        $validate = $this->validateOrder(
            (int)$cart->id,
            $pending_status,
            (float)$cart->getOrderTotal(),
            $this->displayName,
            null,
            array(),
            null,
            false,
            $cart->secure_key
        );

        if ($validate) {
            return new Order($this->currentOrder);
        } else {
            return false;
        }

    }

    public function showBitPagosButton($cart, $order)
    {

        if (!$this->active) {
            return;
        }

        // global $cookie, $smarty; // Use of globals is forbidden

        $ipn_url = _PS_BASE_URL_.__PS_BASE_URI__.'bitpagos_ipn.php';

        $this->context->smarty->assign(array(
            'this_path' => $this->_path,
            'amount' => $cart->getOrderTotal(),
            'reference_id' => $order->id,
            'currency' => 'USD',
            'description' => 'description here',
            'title' => 'title here',
            'form_action' => _PS_MODULE_DIR_ . 'bitpagos/views/templates/front/success.tpl',
            'ipn_url' => $ipn_url,
            'account_id' => Configuration::get('BITPAGOS_ACCOUNT_ID'),
            'api_key' => Configuration::get('BITPAGOS_API_KEY'),
            'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://')
            .htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/'
        ));

        return $this->display(__FILE__, '/views/templates/front/bitpagos_btn.tpl');

    }

    public function hookPayment($params)
    {

        // global $smarty; // Use of globals is forbidden

        $this->context->smarty->assign(array(
            'this_path'         => $this->_path,
            'this_path_ssl'     => Configuration::get('PS_FO_PROTOCOL') .
                                   $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . "modules/{$this->name}/"
        ));

        return $this->display(__FILE__, '/views/templates/hook/payment.tpl');

    }

    public function hookPaymentReturn($params)
    {

    }
}

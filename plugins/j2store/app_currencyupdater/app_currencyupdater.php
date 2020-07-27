<?php
/**
 * --------------------------------------------------------------------------------
 * App Plugin - Currency Updater
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2017 J2Store . All rights reserved.
 * @license     GNU/GPL v3 or latest
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/app.php');
class plgJ2StoreApp_currencyupdater extends J2StoreAppPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element   = 'app_currencyupdater';

    function __construct( &$subject, $config )
    {
        parent::__construct( $subject, $config );
    }

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onJ2StoreGetAppView( $row )
    {

        if (!$this->_isMe($row))
        {
            return null;
        }

        $html = $this->viewList();


        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     *
     * @param $task
     * @return html
     */
    function viewList()
    {
        $app = JFactory::getApplication();

        JToolBarHelper::title(JText::_('J2STORE_APP').'-'.JText::_('PLG_J2STORE_'.strtoupper($this->_element)),'j2store-logo');
        JToolBarHelper::apply ( 'apply' );
        JToolBarHelper::save ();
        JToolBarHelper::back('J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store');
        $vars = new JObject();
        $this->includeCustomModel('AppCurrencyUpdaters');

        $model = F0FModel::getTmpInstance('AppCurrencyUpdaters', 'J2StoreModel');
        $data = $this->params->toArray();
        $newdata = array();
        $newdata['params'] = $data;
        $form = $model->getForm($newdata);
        $vars->form = $form;
        $id = $app->input->getInt('id', 0);
        $vars->id = $id;
        $vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
        $html = $this->_getLayout('default', $vars);
        return $html;
    }

    /**
     * Update currency based on store currency
     * @param $rows - available currency list
     *
    */
    public function onJ2StoreUpdateCurrencies($rows, $force){
        if(count($rows)){
            $store = J2Store::config();
            $store_currency = $store->get('config_currency');
            $db = JFactory::getDbo();
            foreach ($rows as $result) {
                $currency_value = $this->calculateCurrency($store_currency,$result['currency_code'],1);

                if((float)$currency_value){
                    $query = $db->getQuery(true);
                    $query->update('#__j2store_currencies')->set('currency_value ='.$db->q((float)$currency_value))
                        ->set('modified_on='.$db->q(date('Y-m-d H:i:s')))
                        ->where('currency_code='.$db->q($result['currency_code']));
                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
    }

    /**
     * calculate currency value
     * @param $fromCurrency - store currency or base currency
     * @param $toCurrency - other currency code
     * @param $amount - amount to convert
     * @return float - currency value
    */
    function calculateCurrency($fromCurrency, $toCurrency, $amount) {
        //$amount = urlencode($amount);
        $fromCurrency = urlencode($fromCurrency);
        $toCurrency = urlencode($toCurrency);
        //$amount = urlencode($amount);
        $from_Currency = urlencode($fromCurrency);
        $to_Currency = urlencode($toCurrency);
        $api_type = $this->params->get('api_type','msn');
        $converted_amount = 0;
        $url = '';
        if ($api_type == 'msn'){
            $url = "https://www.msn.com/en-us/money/currencydetails/fi-$from_Currency$to_Currency";
        }elseif ($api_type == 'rate_api'){
            $url = "https://ratesapi.io/api/latest?base=$from_Currency&symbols=$to_Currency";
        }elseif ($api_type == 'currency_api'){
            $url = "https://free.currencyconverterapi.com/api/v6/convert?q=".$from_Currency."_".$to_Currency;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $get = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        if ($api_type == 'msn' && !empty($get)){
            $dom = new DOMDocument();
            $dom->loadHTML($get);
            $arr = $dom->getElementsByTagName("li"); // DOMNodeList Object
            $count = 0;

            foreach($arr as $item) { // DOMElement Object
                $class =  $item->getAttribute("class");
                $p_attr = $item->getElementsByTagName("p");
                $title = $p_attr[0]->getAttribute('title');
                $first_count = $count;
                if(strtolower($title) == 'open'){
                    $count = 1;
                }
                if(!empty($first_count) && $first_count == $count ){//&& $class == 'right-align  cc-details-value unchanged'
                    $converted_amount = $title;
                    break;
                }
            }
        }elseif ($api_type == 'rate_api' && !empty($get)){
            //{"base":"USD","date":"2018-12-18","rates":{"INR":70.5397}}
            $currency_data = json_decode($get);
            $currency_rates = isset($currency_data->rates) && !empty($currency_data->rates) ? $currency_data->rates: '';
            if(!empty($currency_rates) && isset($currency_rates->$to_Currency) && !empty($currency_rates->$to_Currency)){
                $converted_amount = $currency_rates->$to_Currency;
            }
        }elseif ($api_type == 'currency_api'){
            //{"query":{"count":1},"results":{"USD_INR":{"id":"USD_INR","val":70.102501,"to":"INR","fr":"USD"}}}
            $currency_data = json_decode($get);
            $currency_name = $from_Currency."_".$to_Currency;
            $currency_data = $currency_data->results;
            $currency_rates = isset($currency_data->$currency_name) && !empty($currency_data->$currency_name) ? $currency_data->$currency_name: '';
            if(!empty($currency_rates) && isset($currency_rates->val)){
                $converted_amount = $currency_rates->val;
            }
        }

        return $converted_amount;
    }

    /**
     * calculate currency value
     * @param $fromCurrency - store currency or base currency
     * @param $toCurrency - other currency code
     * @param $amount - amount to convert
     * @return float - currency value
     */
    function calculateCurrency1($fromCurrency, $toCurrency, $amount) {
        $amount = urlencode($amount);
        $fromCurrency = urlencode($fromCurrency);
        $toCurrency = urlencode($toCurrency);
        $amount = urlencode($amount);
        $from_Currency = urlencode($fromCurrency);
        $to_Currency = urlencode($toCurrency);
        //$base_url = 'https://finance.google.com/bctzjpnsun/converter';
        // $url = $base_url."?a=$amount&from=$from_Currency&to=$to_Currency";
        $url = "https://www.msn.com/en-us/money/currencydetails/fi-$from_Currency$to_Currency";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $get = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        $converted_amount = 0;
        if(!empty($get)){
            $dom = new DOMDocument();
            $dom->loadHTML($get);
            $arr = $dom->getElementsByTagName("li"); // DOMNodeList Object
            $count = 0;

            foreach($arr as $item) { // DOMElement Object
                $class =  $item->getAttribute("class");
                $p_attr = $item->getElementsByTagName("p");
                $title = $p_attr[0]->getAttribute('title');
                $first_count = $count;
                if(strtolower($title) == 'open'){
                    $count = 1;
                }
                if(!empty($first_count) && $first_count == $count ){//&& $class == 'right-align  cc-details-value unchanged'
                    $converted_amount = $title;
                    break;
                }
            }
        }
        return $converted_amount;
    }
}
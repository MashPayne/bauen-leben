<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_webshop_orders
 */

$GLOBALS['TL_DCA']['tl_webshop_orders'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'enableVersioning'            => true,
		'closed'                      => true
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 1,
			'flag'                    => 6,
      'fields'                  => array('datim'),
			'panelLayout'             => 'filter,limit'
    ),
    'label' => array
    (
      'fields'                  => array('pid', 'orderStatus'),
      'format'                  => '%s',
			'label_callback'          => array('tl_webshop_orders', 'formatLabel')
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();"'
      )
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_orders']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
			'reminder' => array(
			  'label'               => &$GLOBALS['TL_LANG']['tl_webshop_orders']['reminder'],
				'href'                => 'key=reminder',
				'icon'                => 'system/modules/webShop/html/icons/reminder.png'
			)
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                 => 'orderStatus,payed,datim'
  ),

  // Subpalettes
  'subpalettes' => array(
    
  ),

  // Fields
  'fields' => array
  (
	  'orderStatus' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_orders']['orderStatus'],
			'inputType'             => 'select',
			'options'               => array('new', 'pending', 'shipped', 'cancel', 'dunning1', 'dunning2', 'encashment'),
			'reference'             => &$GLOBALS['TL_LANG']['tl_webshop_orders']['orderState'],
			'filter'                => true
		),
		'payed' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_orders']['payed'],
			'inputType'             => 'checkbox',
			'filter'                => true
		),
    'pid' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_orders']['pid'],
			'inputType'             => 'select',
			'foreignKey'            => 'tl_member.username'
		),
		'datim' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_orders']['datim'],
			'input_field_callback'  => array('tl_webshop_orders', 'orderDetails'),
			'filter'                => true,
			'flag'                  => 6
		)
  )
);

class tl_webshop_orders extends Backend {
	
	public function orderDetails(DataContainer $objDc) {
		$objTemplate = new FrontendTemplate('webShop_be_orderDetails');
		$objTemplate->label = $GLOBALS['TL_LANG']['tl_webshop_orders']['label'];
    $res = $this->Database->prepare('SELECT * from tl_webshop_orders where id=?')->limit(1)->execute($objDc->id);
		$arrOrderData = $res->row();
		$objTemplate->orderId = $res->id;
		$objTemplate->orderDate = $res->datim;
		$objTemplate->customerIp = $res->ip;
		$objTemplate->customerMsg = $res->orderComment;
		$objTemplate->ts = $res->tstamp;
		$taxes = deserialize($res->taxes);
		
		// addresses
		$objTemplate->paymentAddress = deserialize($res->paymentAddress);
    $objTemplate->shippingAddress = deserialize($res->shippingAddress);
		
		// shipping
		$shipping = deserialize($res->shippingMethodData);
		$shipping['shippingFee'] = $this->formatPrice($shipping['shippingFee'], true);
		$objTemplate->shipping = $shipping;
		$taxes[$shipping['shippingTax']]['sum'] += $shipping['tax'];
		
		// payment
		$payment = deserialize($res->paymentMethodData);
		$payment['paymentFee'] = $this->formatPrice($payment['paymentFee'], true);
		$objTemplate->payment = $payment;
		$taxes[$payment['paymentTax']]['sum'] += $payment['tax'];
    //discount
		$coupons = deserialize($res->coupons);
		foreach($coupons as $i => $coupon)
		  $coupons[$i]['amount'] = stristr($coupon['amount'], '%') ? $coupon['amount'] : $this->formatPrice($coupon['amount'] * (-1), true);
		$objTemplate->coupons = $coupons;
		
    // price
		$objTemplate->billingValue = $this->formatPrice($res->billingValue, true);
		$objTemplate->vat = $res->vatid;
		
		// taxes

		foreach($taxes as $i => $tax)
		  $taxes[$i]['sum'] = $this->formatPrice($tax['sum'], true);
		$objTemplate->taxes = $taxes;
		

		// article
		$arrItems = array();
		$res = $this->Database->prepare('SELECT * from tl_webshop_orderitems where pid=?')->execute($res->id);
		while($res->next()) {
			$arrItems[] = array(
			  'title' => $res->title,
				'subtitle' => $res->subtitle,
				'teaser' => $res->teaser,
				'articleid' => $res->productid,
				'singlePrice' => $this->formatPrice($res->singlePrice, true),
				'qty' => $res->qty,
				'sum' => $this->formatPrice($res->singlePrice * $res->qty, true),
			    'href' => $this->generateEditLink($res->articleid, $res->variantid),
			 	'comment' => $res->articleComment,
				'options' => $res->options
			);
		}
		$objTemplate->items = $arrItems;
		
		if(is_array($GLOBALS['TL_HOOKS']['webShopOrderDetails']) && count($GLOBALS['TL_HOOKS']['webShopOrderDetails']) > 0) {
			foreach($GLOBALS['TL_HOOKS']['webShopOrderDetails'] as $callback) {
				$objCB = new $callback[0]();
				$arrRes = $objCB->$callback[1]($this->Input->get('id'));
				$objTemplate->additionaldata = $arrRes;

			}
		}
		
		// user data
		$r = $this->Database->prepare('SELECT * from tl_member where id=?')->execute($arrOrderData['pid']);
		$objTemplate->userdata = $r->fetchAssoc();
		
		return($objTemplate->parse());
	}
	
	protected function generateEditLink($aid, $vid) {
		$href = $this->Environment->script;
		if($vid == 0)
			$href = sprintf('%s?do=categories&table=tl_webshop_article&act=edit&id=%s', $href, $aid);
		else
		    $href = sprintf('%s?do=categories&table=tl_webshop_articlevariants&act=edit&id=%s', $href, $vid);
		    
		return($href);
	}
	
	public function formatLabel($row, $label) {
	  if($row['pid'] == 0) {
	    /* guest order */
	    $user = deserialize($row['paymentAddress'], true);
	    
	  } else {
			$res = $this->Database->prepare('SELECT * from tl_member where id=?')->execute($row['pid']);
			$user = $res->fetchAssoc();
	  }
    $line = '%s. %s %s [%s] - [%s] [%s]';
		return(sprintf($line, $row['id'], $user['firstname'], $user['lastname'], $this->formatPrice($row['billingValue'], 2), $GLOBALS['TL_LANG']['tl_webshop_orders']['orderState'][$row['orderStatus']], $row['payed'] ? '<span class="payed"/>Bezahlt</span>' : '<span class="notpayed">nicht Bezahlt</span>'));
	  
	}
	
  protected function formatPrice($value, $addSign = false) {
    return(number_format($value, $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimal'], $GLOBALS['TL_CONFIG']['webShop_currencyThausends']) . ($addSign == true ? ' '. $GLOBALS['TL_CONFIG']['webShop_currencySign'] : ''));
  }
	
	public function paymentReminder() {
		require_once(TL_ROOT .'/system/modules/webShop/functions.php');
		$GLOBALS['TL_JAVASCRIPT']['tinyMCE'] = 'plugins/tinyMCE/tiny_mce_gzip.js';
		$this->Import('BackendUser', 'User');
		/* load template and set some defaults */
    $tpl = new BackendTemplate('webShop_paymentReminder');
    $tpl->href = $this->Environment->script .'?do=orders&key=reminder&id='. $this->Input->get('id');
    $tpl->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
    $tpl->action = ampersand($this->Environment->request, true);
    $tpl->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		
		/* read order details and push to template */
		$arrOrder = $this->Database->prepare('SELECT * from tl_webshop_orders where id=?')->execute($this->Input->get('id'))->fetchAssoc();
    $objTemplate = new FrontendTemplate('webShop_reminderMail');
    $objTemplate->datim = $arrOrder['datim'];
    $objTemplate->customer = deserialize($arrOrder['paymentAddress']);
		$objTemplate->price = $arrOrder['billingValue'];
		$tpl->currentStatus = $arrOrder['orderStatus'];
		$objTemplate->user = $this->User;
		$tpl->user = $this->User;
		$tpl->users = $this->Database->prepare('SELECT * from tl_user order by name')->execute()->fetchAllAssoc();
    $orderMail = $objTemplate->parse();
		
		$tpl->message = $orderMail;
		/* send reminder */
		if($this->Input->post('FORM_ACTION') == 'sendReminder') {

      $objEmail = new Email();
			$objEmail->from = $this->User->email;
			$objEmail->fromName = $this->User->name;
			$objEmail->subject = $this->Input->post('reminderSubject');
			$objEmail->html = '<html><head></head><body>'. $this->Input->postRaw('reminderText') .'</body></html>';
			$sendBCC = $this->Input->post('sendBCC');
			if(is_array($sendBCC) && count($sendBCC))
			  foreach($sendBCC as $bcc)
				  $objEmail->sendCc($bcc);
					
			$objEmail->sendTo($arrOrder['email']);						
		  /* update order status */
		  if($this->Input->post('newStatus') != $arrOrder['orderStatus'])
			  $this->Database->prepare('UPDATE tl_webshop_orders set orderStatus=? where id=?')->execute($this->Input->post('newStatus'), $arrOrder['id']);
      
			$tpl = new BackendTemplate('webShop_paymentReminder_send');
	    $tpl->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
	    $tpl->href = $this->Environment->script .'?do=orders';
	    $tpl->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
		}
		
    /* return template */
    return($tpl->parse());
	}
	
}

?>
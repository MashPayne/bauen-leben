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
 * @copyright  Stefan Gandlau 2008 
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    EULA 
 * @filesource
 */


/**
 * Class webShopPaymentController
 *
 * @copyright  Stefan Gandlau 2008
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopPaymentController extends Controller {

    protected $payments = array();
		protected $selected = 0;
		protected $cartPrices = array();
		protected $taxes = array();
		
    public function __construct() {
    	$this->Import('Database');
    	$this->Import('Input');
    	if(FE_USER_LOGGED_IN)
        	$this->Import('FrontendUser', 'User');

		$this->payments = $this->readPaymentOptions();
		parent::__construct();
    }
		
	protected function readPaymentOptions() {
		$arrOptions = array();
		if(FE_USER_LOGGED_IN)
  			$_groups = $this->User->groups;
  	  	else
  	    	$_groups = array();
  	    	
		$res = $this->Database->prepare('SELECT * from tl_webshop_paymentmodules where published=?')->execute(1);
		if($res->numRows == 0) return($arrOptions);
		while($res->next()) {
			$groups = deserialize($res->groups);
			if(!is_array($groups)) continue;
			if(array_intersect($_groups, $groups) || $res->guestAllowed) {
				if($this->selected == 0)
				  $this->selected = $res->id;
				$arrOptions[$res->id] = $res->row();
			}
		}
		return($arrOptions);
	}
		
		protected function getFirst() {
			
		}
		
		protected function calculatePaymentFee($arrPayment) {
			$taxes = $this->Database->prepare('SELECT * from tl_webshop_taxes WHERE id=?')->execute($arrPayment['paymentTax'])->fetchAssoc();
		  
			$discount = $arrPayment['discount'];
			$ar1 = array('+', '-', '%');
			$ar2 = array('', '', '');
			switch(substr($discount, 0, 1)) {
				case '-': {
					if(stristr($discount, '%')) {
						$arrPayment['netto'] = ($this->cartPrices['netto'] * (str_replace($ar1, $ar2, $discount) / 100) * (-1));
            			$arrPayment['brutto'] = ($this->cartPrices['brutto'] * (str_replace($ar1, $ar2, $discount) / 100) * (-1));
						$arrPayment['tax']  = $arrPayment['brutto'] - $arrPayment['netto'];
					} else {
						if($GLOBALS['TL_CONFIG']['webShop_pricesButto']) {
							$arrPayment['brutto'] = $discount;
							$arrPayment['netto'] = $discount / ($taxes['tax_rate'] / 100 + 1);
							$arrPayment['tax'] = $arrPayment['brutto'] - $arrPayment['netto'];
						} else {
							$arrPayment['netto'] = $discount;
							$arrPayment['brutto'] = $discount * ($taxes['tax_rate'] / 100 + 1);
							$arrPayment['tax'] = $arrPayment['brutto'] - $arrPayment['netto'];		
						}
					}
				} break;
				
				default: {
					if(stristr($discount, '%')) {
            			$arrPayment['netto'] = ($this->cartPrices['netto'] * (str_replace($ar1, $ar2, $discount) / 100));
			            $arrPayment['brutto'] = ($this->cartPrices['brutto'] * (str_replace($ar1, $ar2, $discount) / 100));
			            $arrPayment['tax']  = $arrPayment['brutto'] - $arrPayment['netto'];
		          	} else {
		          		if($GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
			          		$arrPayment['brutto'] = $discount;
				            $arrPayment['netto'] = $discount / (1 + ($taxes['tax_rate'] / 100));
				            $arrPayment['tax'] = $arrPayment['brutto'] - $arrPayment['netto'];
		          		} else {
			          		$arrPayment['netto'] = $discount;
				            $arrPayment['brutto'] = $discount * (1 + ($taxes['tax_rate'] / 100));
				            $arrPayment['tax'] = $arrPayment['brutto'] - $arrPayment['netto'];
		          		}
            			
			        }
				} break;
			}
			
			return($arrPayment);
		}
		
		public function doPreselection() {
			
			foreach($this->payments as $index => $payment) {
				$paymentPrice = '';
				$paymentId = 0;
				
				if($paymentPrice == '' || $paymentPrice > $payment['paymentFee']) {
					$paymentId = $index;
					$paymentPrice = $payment['paymentFee'];
				}
			}
			$this->selected = $paymentId;

			$_SESSION['webShop']['checkout']['paymentMethod'] = $paymentId;
		}
		
		public function __get($key) {
			switch(strtolower($key)) {
				case 'options':
        		case 'paymentoptions': {
        			return($this->payments);
        		} break;
				case 'hasoptions': return(count($this->payments) ? true : false); break;
				case 'selected':
        		case 'selectedoption':
				case 'option': return($this->calculatePaymentFee($this->payments[$this->selected])); break;
				case 'selectedid': return($this->selected); break;
				case 'paymentfee': {
					return($this->calculatePaymentFee());
				} break;
			}
		}
		
		public function __set($key, $value) {
			switch(strtolower($key)) {
				case 'id':
        		case 'option':
        		case 'payment': $this->selected = $value; break;
				case 'select': $this->selected = $value; break;
				case 'totalprice': $this->cartPrices = $value; break;
			}
		}

  }

?>
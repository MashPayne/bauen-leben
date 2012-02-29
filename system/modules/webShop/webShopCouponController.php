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
 * Class webShopCouponController
 *
 * @copyright  Stefan Gandlau 2008
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopCouponController extends Controller {

    protected $arrCoupons = array();
		protected $arrError = array();

    public function __construct() {
    	$this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
			
			if(!is_array($_SESSION['webShop']['coupons']))
			  $_SESSION['webShop']['coupons'] = array();
				
			$this->arrCoupons = $this->loadCoupons();
			return parent::__construct();
    }
		
		public function __destruct() {
			$_SESSION['webShop']['coupons'] = array_keys($this->arrCoupons);
		}
		
		public function addCode($code) {
		  if($GLOBALS['TL_CONFIG']['webShop_disableCoupons'] == '1') return false;
      
			$code = trim($code);
			if(!strlen($code)) {
				$this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['no_code'];
				return(false);
			}
			if(in_array($code, array_keys($this->arrCoupons))) {
				$this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['already_added'];
				return(false);
			}
			$coupon = $this->couponData($code);
			if(is_array($coupon)) {
				$this->arrCoupons[$code] = $coupon;
				return(true);
			} else {
				return(false);
			}
		}
		
		public function removeCode($code) {
			if(in_array($code, $this->arrCoupons))
			  unset($this->arrCoupons[$code]);
		}
		
		protected function couponData($code) {
		  if($GLOBALS['TL_CONFIG']['webShop_disableCoupons'] == '1') return false;
			$res = $this->Database->prepare('SELECT * from tl_webshop_coupons where published=? AND code=?')->executeUncached(1, $code);
			if($res->numRows == 0) {
				 $this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['code_not_found'];
				 return(false);
			}
      
			$arrRow = $res->fetchAssoc();
			// check type & user
			if($arrRow['type'] == 'couponUser') {
				if($arrRow['userid'] != $this->User->id) {
					$this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['code_not_found'];
					return(false);
				}
			}
			
			// check validity
			if(strlen($arrRow['validUntil']) && $arrRow['validUntil'] < time()) {
				$this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['outdated'];
				return(false);
			}
			  
				
			// check maxUse
			if(strlen($arrRow['limitUse']) && $arrRow['maxUse'] == 0) {
				$this->arrError[] = $GLOBALS['TL_LANG']['webShop']['coupon_errors']['limit_exceeded'];
			  return(false);
			}
				
			// ok.. everything looks good
			return($arrRow);
		}
		
		public function __get($key) {
			switch(strtolower($key)) {
				case 'hascoupon': return(count($this->arrCoupons) > 0 ? true : false); break;
				case 'sum':
				case 'couponsum': {
					$cSum = 0;
					if(count($this->arrCoupons) > 0)
  					foreach($this->arrCoupons as $c)
					   $cSum += $c['amount'];
					return($cSum);
				} break;
				case 'coupons':
				case 'codes':
        case 'couponcodes': return($this->arrCoupons); break;
				case 'haserror': return(count($this->arrError) > 0 ? true : false); break;
				case 'error':
        case 'errors':
				case 'geterror':
				case 'geterrors': return($this->arrError); break;
			}
		}
		
		protected function applyCoupons() {
		  
			if(count($this->arrCoupons)) {
				foreach($this->arrCoupons as $coupon) {
					if(stristr($coupon['amount'], '%')) {
						$amount = str_replace('%', '', $coupon['amout']);
						$value = $this->totalSum * ($amount / 100);
					} else
					  $value = $coupon['amount'];
						
					
				}
			}
		}
		
		protected function loadCoupons() {
			$arrC = array();
			$c = $_SESSION['webShop']['coupons'];
			if(!is_array($c)) return($arrC);
      foreach($_SESSION['webShop']['coupons'] as $code) {
      	$tmp = $this->couponData($code);
				if(is_array($tmp))
				  $arrC[$code] = $tmp;
				else
				  $this->removeCode($code);
      }
			return($arrC);
		}


  }

?>
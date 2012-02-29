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
 * @license    EULA 
 * @filesource
 */


/**
 * Class ModuleWebShopOrderCompleted
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleWebShopOrderCompleted extends Module {
    
    protected $strTemplate = 'webShop_orderCompleted';

    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### ORDER COMPLETED ###';
        return($t->parse());
      }
			
			$this->Import('FrontendUser', 'User');
      return(parent::generate());
    }
    
    protected function compile() {
      $orderId = $this->Input->get('orderId');
			$orderKey = $this->Input->get('orderKey');
			
			if(!strlen($orderId) || !strlen($orderKey))
			  die('forbidden no_data');
			
			$orderDetails = $this->readOrderDetails($orderId, $orderKey);
			if($orderDetails) {
				// get payment module
				$res = $this->Database->prepare('SELECT * from tl_webshop_paymentmodules where id=?')->execute($orderDetails['paymentMethodData']['id']);
				$arrPayment = $res->fetchAssoc();
				if(strlen($arrPayment['paymentModule'])) {
				  $this->Template->paymentText = $arrPayment['paymentText'];
					$this->Template->payment = $this->compilePayment($arrPayment, $orderDetails);
				}
			} else {
				// invalid order-key or order not found
				
			}
    }
		
		protected function compilePayment($arrPayment, $order) {
			$moduleBase = TL_ROOT .'/system/modules/webShop/paymentModules/';
			$module = $arrPayment['paymentModule'];
			$config = deserialize($arrPayment['paymentConfig']);
			if(is_dir($moduleBase . $module) && file_exists($moduleBase . $module .'/'. $module .'.php')) {
				require_once($moduleBase . $module .'/'. $module .'.php');
				$objPayment = new $module($config);
				$objPayment->data = $order;
				return($objPayment->compile());
			}
		}
		
		protected function readOrderDetails($id, $key) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_orders where id=?')->execute($id);
			if($res->numRows == 0) return(false);
			$arrOrder = $res->fetchAssoc();
			if($key != md5($GLOBALS['TL_CONFIG']['encryptionKey'] . $arrOrder['datim'])) return(false);
			
			// unpack arrays
			$arrOrder['shippingAddress'] = deserialize($arrOrder['shippingAddress']);
			$arrOrder['shippingMethodData'] = deserialize($arrOrder['shippingMethodData']);
			$arrOrder['paymentMethodData'] = deserialize($arrOrder['paymentMethodData']);
			$arrOrder['paymentAddress'] = deserialize($arrOrder['paymentAddress']);
			$arrOrder['taxes'] = deserialize($arrOrder['taxes']);
			$arrOrder['coupons'] = deserialize($arrOrder['coupons']);
			
			// return order array
			return($arrOrder);
		}
    
  }

?>
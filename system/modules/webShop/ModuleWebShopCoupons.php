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
 * @copyright  Stefan Gandlau 2011
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    EULA 
 * @filesource
 */


/**
 * Class ModuleWebShopCoupons
 *
 * @copyright  Stefan Gandlau 2011
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */ 

  class ModuleWebShopCoupons extends Module {
  
  	protected $strTemplate = 'webshop_coupons';
  	
  	public function generate() {
  		if(TL_MODE == 'BE') {
  			$objT = new BackendTemplate('be_wildcard');
  			$objT->wildcard = 'WEBSHOP COUPONS';
  			return($objT->parse());
  		}
  		
  		if($GLOBALS['TL_CONFIG']['webShop_disableCoupons'] == '1') return;
  		
  		$this->Import('webShopCouponController', 'Coupons');
  		
  		
		if(strlen($this->Input->post('couponcode')))
			$this->Coupons->addCode($this->Input->post('couponcode'));
			
  		return(parent::generate());
  	}
  	
  	protected function compile() {
  		$this->Template->couponErrors = $this->Coupons->getErrors;
  		$this->Template->coupons = $this->Coupons->coupons;
  		$this->Template->sum = $this->Coupons->sum;
  		
  		$this->Template->href = $this->Environment->request;
  		$this->Template->labelCode = $GLOBALS['TL_LANG']['webShop']['coupon'];
  		$this->Template->labelSubmit = $GLOBALS['TL_LANG']['webShop']['coupon_submit'];
   	}
  
  }

?>
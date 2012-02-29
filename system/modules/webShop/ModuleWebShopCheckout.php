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
 * Class ModuleWebShopCheckout
 *
 * @copyright  Stefan Gandlau 2009-2012
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

	require_once('functions.php');

	class ModuleWebShopCheckout extends Module {
    
    	protected $strTemplate = 'webShop_checkout';
    	protected $shippingRequired = true;
    	protected $jumpBook = array();
    	
    	public function generate() {
      		if(TL_MODE == 'BE') {
        		$t = new BackendTemplate('be_wildcard');
        		$t->wildcard = '### WEBSHOP CHECKOUT ###';
        		return($t->parse());
      		}
			
			if(!FE_USER_LOGGED_IN && !strlen($GLOBALS['TL_CONFIG']['webShop_guestOrder'])) {
        		$objHandler = new $GLOBALS['TL_PTY']['error_403']();
        		$objHandler->generate($pageId);
        		return;
			}
				
			if($this->Input->post('FORM_ACTION') == 'webShopCheckout') {

					
		    	if($this->Input->post('shippingAddress') != '')
					$_SESSION['webShop']['checkout']['shippingAddress'] = $this->Input->post('shippingAddress');
					
			  	if($this->Input->post('paymentMethod') != '')
					$_SESSION['webShop']['checkout']['paymentMethod'] = $this->Input->post('paymentMethod');
					
			  	if($this->Input->post('shippingMethod') != '')
					$_SESSION['webShop']['checkout']['shippingMethod'] = $this->Input->post('shippingMethod');
					
      		}
			
			$this->Import('FrontendUser', 'User');
      		$this->Import('webShopTaxController', 'Tax');
			$this->Import('webShopShoppingCart', 'Cart');
			$this->Cart->taxes = $this->Tax->taxes;
			$this->Import('webShopAddressBook', 'Book');
			$this->Import('webShopShippingController', 'Shipping');
			$this->Import('webShopPaymentController', 'Payment');
			
			if(!strlen($_SESSION['webShop']['checkout']['shippingAddress']))
				$_SESSION['webShop']['checkout']['shippingAddress'] = $this->Book->primaryid;
			  
			if(!strlen($_SESSION['webShop']['checkout']['paymentMethod']))
				$this->Payment->doPreselection();
			  
			
			$this->Book->select = $_SESSION['webShop']['checkout']['shippingAddress'];
			$this->Shipping->select = $_SESSION['webShop']['checkout']['shippingMethod'];
			$this->Payment->select = $_SESSION['webShop']['checkout']['paymentMethod'];
			$this->jumpBook = $this->Database->prepare('SELECT id,alias from tl_page WHERE id=?')->execute($this->webShop_jumpToAddressBook)->fetchAssoc();
      		return(parent::generate());
    	}
    
    	protected function compile() {
    		$objAddressTemplate = new FrontendTemplate('webshop_checkout_address');
    		$objShippingTemplate = new FrontendTemplate('webshop_checkout_shipping');
    		$objPaymentTemplate = new FrontendTemplate('webshop_checkout_payment');
    	
			if(!FE_USER_LOGGED_IN && $GLOBALS['TL_CONFIG']['webShop_guestOrder']) {
		
				$guest = $this->Input->get('guest');
				if($_SESSION['isGuestOrder'])
					$guest = true;
					
				if(!strlen($guest)) {
			  		$this->Template = new FrontendTemplate('webShop_checkout_guestpre');
			  		$this->Template->href = $this->Environment->request;
			  		$this->Template->labelGuestContinue = $GLOBALS['TL_LANG']['webShop']['btnGuestContinue'];
			  		return;
				} else {
					$_SESSION['isGuestOrder'] = true;
					if(file_exists(TL_ROOT .'/system/modules/webShop/html/guest.css'))
				    	$GLOBALS['TL_CSS']['guestcss'] = 'system/modules/webShop/html/guest.css';
				}
		
	        	$this->Book->guestOrder = true;
	        	$objAddressTemplate->guestOrder = true;
	        	$objShippingTemplate->guestOrder = true;
	        	$objPaymentTemplate->guestOrder = true;
	        	$objAddressTemplate->frmShippingAddress = $this->Book->guestShipping();
	        	$objAddressTemplate->frmBillingAddress = $this->Book->guestBilling();
	        	$objAddressTemplate->errorBilling = $this->Book->errorBilling;
	        	$objAddressTemplate->errorShipping = $this->Book->errorShipping;
	        	$objAddressTemplate->lblConfirmAddress = $GLOBALS['TL_LANG']['webShop']['lbl_confirmAddress'];
	        	$this->Book->reloadAddressBook();
	    	}
	    	$objAddressTemplate->linkAddressbook = $this->generateFrontendUrl($this->jumpBook);
    		$GLOBALS['TL_JAVASCRIPT']['webShopCheckout'] = 'system/modules/webShop/html/checkout.js';
			$arrAddresses = $this->Book->addresses;
    		$arrCartItems = $this->Cart->getItems();
			if(count($this->Cart->warnings)) {
				$this->redirect($this->generateFrontendUrl($this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCart'])->fetchAssoc()));
			}
		
			/* preselect payment & shipping */

		
			if($this->Tax->showBrutto)
  				$cartPrice = $this->Cart->brutto;
			else
		  		$cartPrice = $this->Cart->netto;
			
			$this->Template->cartPrice = $cartPrice;
  			$objAddressTemplate->addAddress = $this->frmEditAddress();
			$this->Shipping->cartItems = $arrCartItems;
			$this->Shipping->country = $arrAddresses[$this->Book->selected]['country'];
		
			if(!$this->Shipping->selected)
		  		$this->Shipping->doPreselection();
		  
			$objShippingTemplate->shippings = $this->Shipping->options;
			$shippingOption = $this->Shipping->selectedoption;
			$objShippingTemplate->shippingMethod = $shippingOption;
			$objAddressTemplate->addresses = $arrAddresses;
  			$objAddressTemplate->shippingAddressId = $this->Book->selected;
			$objPaymentTemplate->payments = $this->Payment->options;
      
			$payment = $this->Payment->selected;
			if($payment['paymentModule']) {
				$mod = $payment['paymentModule'];
				require_once(TL_ROOT .'/system/modules/webShop/paymentModules/'. $mod .'/'. $mod .'.php');
				$objPayment = new $mod(deserialize($payment['paymentConfig']));
				if(!$objPayment->check())
    		    	$objPaymentTemplate->paymentMessage = $objPayment->getError();
			}
	    	$objPaymentTemplate->paymentMethod = $this->Payment->selected;
      	

      
			$totalPrice += $shippingPrice;
			$this->Template->totalPrice = $totalPrice;
      
	      	if(!FE_USER_LOGGED_IN && $GLOBALS['TL_CONFIG']['webShop_guestOrder']) {
    	  		if($this->Book->addressOK == true) {
      				$objAddressTemplate->addressOK = true;
      				$objShippingTemplate->addressOK = true;
      				$objPaymentTemplate->addressOK = true;
      				$this->Template->addressOK = true;
      			}
      		}
      
			$checkoutOK = (count($this->Book->errorBilling) < 1 && count($this->Book->errorShipping) < 1 && is_numeric($this->Book->selected) && ($noShipping || $this->Shipping->selected > 0) && is_array($this->Payment->selected) && (is_object($objPayment) && $objPayment->check()));

			$this->Template->checkoutOk = $checkoutOK;
			$this->Template->addresses = $objAddressTemplate->parse();
			$this->Template->shipping = $objShippingTemplate->parse();
			$this->Template->payment = $objPaymentTemplate->parse();
		
			$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCart']);
			$this->Template->lnkCart = $this->generateFrontendUrl($res->fetchAssoc());
		
			$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
			$arrJumpTo = $res->fetchAssoc();
		
			$this->Template->href = $this->Environment->request;
		

			if($this->Input->post('checkoutContinue') && $checkoutOK) {
				$this->redirect($this->generateFrontendUrl($arrJumpTo));
			}
    	}
		
    	protected function frmEditAddress($idAddress = false) {
    	
      		if(is_numeric($idAddress))
        		$arrAddress = $this->Book->addresses[$idAddress];

      		$submit = true;     
      		$arrFields = $this->Book->fields;
      		$arrElem = array();
      		foreach($arrFields as $fld) {
        		$arrData = $GLOBALS['TL_DCA']['tl_member']['fields'][$fld];
        		$strClass = $GLOBALS['TL_FFL'][$arrData['inputType']];
        		if(!$this->classFileExists($strClass)) continue;
        		$objWdg = new $strClass($this->prepareForWidget($arrData, $fld));
        		$objWdg->required = $arrData['eval']['mandatory'];
        		if($this->Input->post('FORM_SUBMIT') == 'saveAddress') {
          			$objWdg->value = $this->Input->post($fld);
          			$objWdg->validate();
          			$objWdg->tableless = true;
          			if($objWdg->hasErrors())
            			$submit = false;
        		} else
          			if(is_array($arrAddress)) $objWdg->value = $arrAddress[$fld];
        
        		$arrElem[] = $objWdg->parse();
      		}
      
      		if($submit && $this->Input->post('saveAddress') != '' && $this->Input->post('shippingAddress') == 'new') {
        		$_SESSION['webShop']['checkout']['shippingAddress'] = $this->Book->saveAddress();
        		$this->reload();
      		}
     		return($arrElem);
    	}
	}

?>
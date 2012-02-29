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
 * Class ModuleWebShopCart
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  require_once('functions.php');

  class ModuleWebShopCart extends Module {
    
    protected $strTemplate = 'webShop_cart_default';
		
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### WEBSHOP SHOPPING CART ###';
        return($t->parse());
      }
			
			if(FE_USER_LOGGED_IN || $GLOBALS['TL_CONFIG']['webShop_guestOrder'] == '1') {
  				$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
			} else {
				$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->webShop_jumpLogin);
			}
			$lnkCheckout = $this->generateFrontendUrl($res->fetchAssoc());
			
			if(!is_array($_SESSION['webShop']['cart']))
			  $_SESSION['webShop']['cart'] = array();
				
			$this->Import('webShopShoppingCart', 'objCart');
			$this->objCart->isCart = true;
			
			$this->Import('webShopTaxController', 'Tax');
			$this->objCart->taxes = $this->Tax->taxes;
			if($this->Input->post('FORM_ACTION') == 'webShopUdateCart') {
				$arrRemove = $this->Input->post('cartItemRemove');
				$arrQTY = $this->Input->post('itemQTY');
				$comments = $this->Input->post('itemComment');
				if(is_array($arrQTY))
				  foreach($arrQTY as $index => $newQTY)
				    $this->objCart->updateQTY($index, $newQTY);
				
	   			if(is_array($comments))
	   			  foreach($comments as $index => $comment)
	   			    $this->objCart->updateComment($index, $comment);
				    
				if(is_array($arrRemove)) 
	  				foreach(array_keys($arrRemove) as $index)
		   			  $this->objCart->removeItem($index);
	   			  

            if($this->Input->post('doCheckout'))
              $this->redirect($lnkCheckout);
              
          $this->reload();
        }
      return(parent::generate());
    }
    
    protected function compile() {
		if(strlen($this->webShop_cartTemplate)) {
			$this->strTemplate = $this->webShop_cartTemplate;
			$this->Template = new FrontendTemplate($this->webShop_cartTemplate);
		}
			$arrSize = deserialize($this->webShop_thumbSize);
			$imgWidth = false;
			$imgHeight = false;
			if($arrSize[0]) $imgWidth = $arrSize[0];
      if($arrSize[1]) $imgHeight = $arrSize[1];
      
			$this->objCart->imageConfig = array('width' => $imgWidth, 'height' => $imgHeight);
    	$arrItems = $this->objCart->getItems();
			$arrWarnings = $this->objCart->warnings;
			if(count($arrWarnings))
			  $this->Template->cartWarnings = $arrWarnings;
      foreach($arrItems as $id => $item) {
      	$arrItems[$id]->thumb = $this->getImage($item->singleSRC, $imgWidth, $imgHeight);
      }
      $this->Template->cartItems = $arrItems;
			
			if($this->Tax->showBrutto)
    		$this->Template->totalPrice = $this->objCart->brutto;
			else
			  $this->Template->totalPrice = $this->objCart->netto;

			 
  			$this->Template->taxes = $this->objCart->tax;
			$this->Template->totalTax = $this->objCart->sumtax;
			


			$this->Template->href = $this->Environment->request;
			$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->getRootIdFromUrl());
			$this->Template->lnkContinue = $this->generateFrontendUrl($res->fetchAssoc());
    }
    
  }

?>
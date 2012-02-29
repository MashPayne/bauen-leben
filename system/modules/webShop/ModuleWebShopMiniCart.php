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
 * Class ModuleWebShopMiniCart
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  require_once('functions.php');

  class ModuleWebShopMiniCart extends Module {
    
    protected $strTemplate = 'webShop_minicart';

    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '###  ###';
        return($t->parse());
      }
      $this->Import('webShopTaxController', 'Tax');
			$this->Import('webShopShoppingCart', 'Cart');
      return(parent::generate());
    }
    
    protected function compile() {
      $this->Cart->taxes = $this->Tax->taxes;
      $this->Cart->getItems();
      if($this->Cart->Count  > 0) $this->Template->cartActive = 'cart_active ';
      $this->Template->itemCount = $this->Cart->count;
      if($this->Tax->showBrutto)
        $cartPrice = $this->Cart->brutto;
      else
        $cartPrice = $this->Cart->netto;
      
      $this->Template->cartPrice = $cartPrice;
      
			if($this->jumpTo) {
				$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
				$this->Template->linkCart = $this->generateFrontendUrl($res->fetchAssoc());
			}
    }
    
  }

?>
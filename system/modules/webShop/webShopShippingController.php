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
 * Class webShopShippingController
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopShippingController extends Controller {

    protected $shipping = array();
		protected $shippingCountry;
		protected $cartItems = array();
		
		protected $selected = 0;
		public $specialShipping = 0;
		
    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
				
			parent::__construct();
    }
    
    public function doPreselection() {
		if(!is_array($this->shipping) || count($this->shipping) == 0) return;
    	foreach($this->shipping as $index => $shipping) {
			break;
		}
			$this->selected = $index;

			$_SESSION['webShop']['checkout']['shippingMethod'] = $index;;
    }
		
		public function calculateShippingPrice() {
			$arrItems = $this->cartItems;
			if(!is_array($this->shipping) || !count($this->shipping)) return;
			  
			foreach($this->shipping as $methodid => $method) {
				switch($method['shippingPriceType']) {
					case 'shippingInfo': {
						//$this->shipping[$methodid]['shippingFee'] = $method['shippingInfo'];
						$this->shipping[$methodid]['shippingFee'] = 0;
					} break;
					case 'shippingByPrice': {

						$price = 0;

						foreach($arrItems as $item) {

  								$price += $item->sumNetto + $item->sumTax;
						}
						$sp = 0;
						$arrScale = deserialize($method['shippingPricesPrice']);
						for($x = 0; $x < count($arrScale); $x++) {
							if($price >= $arrScale[$x]['value']) {
								$sp = $arrScale[$x]['label'];
							}
						}
						$this->shipping[$methodid]['shippingFee'] = $sp;
					} break;
					case 'shippingByWeight': {
						$weight = 0;
						foreach($arrItems as $item)
				
						if($item->weight > 0) 
							$weight += $item->weight * $item->qty; 
			
						$arrScale = deserialize($method['shippingPricesWeight']);
						for($x = 0; $x < count($arrScale); $x++) {
							$w = $arrScale[$x]['value'];
							$p = $arrScale[$x]['label'];
							if($weight >= $w) {
								$price = $p;
							}
						}
						$this->shipping[$methodid]['shippingFee'] = $price;
					} break;
					default: $this->shipping[$methodid]['shippingFee'] = 0;
				}
			}

		}
		
		protected function loadShippingOption() {
			$arrOptions = array();
			$zone = $this->findShippingZone();
			if(!$zone)
			  return(false);
			$res = $this->Database->prepare('SELECT * from tl_webshop_shippingoptions')->execute();
			if($res->numRows == 0) return(false);
			while($res->next()) {
				$arrZones = deserialize($res->shippingZones);
				if(in_array($zone, $arrZones)) {
					if($this->specialShipping > 0 && $res->id != $this->specialShipping) continue;
					$opt = $res->row();
					$arrOptions[$res->id] = $opt;
				}
			}
			return(count($arrOptions) ? $arrOptions : false);
		}
		
		protected function findShippingZone() {
			$res = $this->Database->prepare('SELECT * from tl_webshop_shippingzones')->execute();
			if($res->numRows == 0) return(false);
			while($res->next()) {
				
				$arrCountries = deserialize($res->shippingCountries);
				if(is_array($arrCountries) && in_array($this->shippingCountry, $arrCountries))
				  return($res->id);
			}
			return(false);
		}

    public function __set($key, $value) {
      switch($key) {
        case 'country':
        case 'shippingcountry': {
          $this->shippingCountry = $value;
          $this->shipping = $this->loadShippingOption();
					$this->calculateShippingPrice();
        } break;
				case 'cartItems': $this->cartItems = $value; break;
				case 'method': $this->shippingMethod = $value; break;
				case 'select': $this->selected = $value; break;
      }
    }
    
    public function __get($key) {
      if(!is_array($this->shipping))
        return(false);
      switch(strtolower($key)) {
      	case 'options':
        case 'methodes': return($this->shipping); break;
        case 'selected': return($this->selected); break;
				case 'selectedoption': return($this->shipping[$this->selected]); break;
        default: return(in_array($key, array_keys($this->shipping)) ? $this->shipping[$key] : false);
      }     
    }
    
    public function noShippingItems() {
      $noshipping = true;
      foreach($this->cartItems as $item) {
        if($item->type != 'download') {
          $noshipping = false;
        }
      }
      return($noshipping);
    }
		
  }

?>
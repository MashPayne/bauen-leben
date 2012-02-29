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
  * @copyright  Stefan Gandlau 2009-2012
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  * @license    EULA 
  * @filesource
  */
  
  
  /**
  * Class webShopShoppingCart
  *
  * @copyright  Stefan Gandlau 2009-2012
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  */
  
  class webShopShoppingCart extends Controller {
    
    protected $configuration = array();
		protected $totalNetto = 0;
		protected $totalBrutto = 0;
		protected $totalTax = 0;
		protected $arrTaxes = array();
		protected $itemCount = 0;
		protected $items = array();
		public $isCart = false;
		public $warnings = array();
		
		protected $imageConfig = array();
    
    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
        
      if(!is_array($_SESSION['webShop']['cart']))
        $_SESSION['webShop']['cart'] = array();
      
        
      parent::__construct();
    }
    
    public function updateComment($index, $comment) {
    	$_SESSION['webShop']['cart'][$index]['comment'] = $comment;
    }
    
    public function getItems() {
      $arrItems = array();
			$arrXItems = array();
			$hasDownloads = false;
      if(count($_SESSION['webShop']['cart']) == 0) return(array());
			
      foreach($_SESSION['webShop']['cart'] as $index => $item) {
      	
      	$res = $this->Database->prepare('SELECT * from tl_webshop_article where id=?')->execute($item['id']);

				if($res->numRows == 0) {
					unset($_SESSION['webShop']['cart'][$index]);
					continue;
				}
				
        $dbData = $res->fetchAssoc();
        if($dbData['type'] == 'download')
          $hasDownloads = true;
      	$arrXItems[$index] = new webShopArticle();
				$arrXItems[$index]->imageConfig = $this->imageConfig;
				$arrXItems[$index]->dataArray = $dbData;
				$arrXItems[$index]->qty = $item['qty'];
				$arrXItems[$index]->comment = $item['comment'];

		$arrXItems[$index]->arrOptions = $item['options'];
        $arrXItems[$index]->taxes = $this->arrTaxes;
        
        // Artikel nicht mehr published, aus warenkorb entfernen und meldung ausgeben
        if(!$arrXItems[$index]->isPublished()) {
          $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['cart_itemUnavailable'], $arrXItems[$index]->title);
					if($this->isCart) {
            $this->removeItem($index);
	   				unset($arrXItems[$index]);
					}
          continue;
        }
        
        // Artikel lagerbestand
        if($arrXItems[$index]->addStock) {
          if($arrXItems[$index]->stock <= 0) {  // ausverkauft
            if($arrXItems[$index]->hideIfEmpty) {  //ausblenden
              $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['cart_itemUnavailable'], $arrXItems[$index]->title);
							if($this->isCart) {
                $this->removeItem($index);
                unset($arrXItems[$index]);
							}
							continue;
            } else { // nicht ausblenden
              $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['cart_stockInfo1'], $arrXItems[$index]->title);
            }
          } else { // nicht ausverkauft
            if($arrXItems[$index]->stock < $item['qty']) { // bestand geringer als gewuenscht 
              if($arrXItems[$index]->hideIfEmpty) {
	              $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['cart_stockInfo2'], $arrXItems[$index]->title);
								if($this->isCart) {
									$_SESSION['webShop']['cart'][$index]['qty'] = $arrXItems[$index]->stock;
								}
								// Reload
								$arrXItems[$index] = new webShopArticle();
				        $arrXItems[$index]->imageConfig = $this->imageConfig;
				        $arrXItems[$index]->dataArray = $dbData;
				        $arrXItems[$index]->qty = $arrXItems[$index]->stock;
				        if($item['vid'])
				          $arrXItems[$index]->selectVariant = $item['vid'];
				        $arrXItems[$index]->taxes = $this->arrTaxes;
							} else {
								$this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['cart_stockInfo1'], $arrXItems[$index]->title);
							}
            }           
          }
        }
				
				$this->arrTaxes[$arrXItems[$index]->taxid]['sum'] += $arrXItems[$index]->sumTax;
				$this->totalTax += $arrXItems[$index]->sumTax;
				$this->totalNetto += $arrXItems[$index]->sumNetto;
				$this->totalBrutto += ($arrXItems[$index]->sumNetto + $arrXItems[$index]->sumTax);

      }
			$this->items = $arrXItems;
			
			if($GLOBALS['TL_CONFIG']['webShop_mov'] > $this->sum) {
			  $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['mov'], formatPrice($GLOBALS['TL_CONFIG']['webShop_mov'], true));
			}
			
			if(!FE_USER_LOGGED_IN && $hasDownloads) {
			  $this->warnings[] = sprintf($GLOBALS['TL_LANG']['webShop']['guestDownloads']);
			}
      return($arrXItems);
    }
    
    public function updateQTY($index, $newQTY) {
      if(!is_numeric($newQTY)) return;
      if($newQTY < 1)
        return($this->removeItem($index));
      if(is_array($_SESSION['webShop']['cart'][$index]))
        $_SESSION['webShop']['cart'][$index]['qty'] = $newQTY;
    }


    
    public function removeItem($index) {
      if(is_array($_SESSION['webShop']['cart'][$index]))
        unset($_SESSION['webShop']['cart'][$index]);
    }

    
    public function addItem() {
      $articleID = $this->Input->get('articleid');
      $variantID = $this->Input->get('articlevariant');
      $arrOptions = $this->Input->get('wsOption');

      
      $index = $this->findCartItem($articleID, $variantID, $arrOptions);
			if($index == -1)
			  return(false);
      $_SESSION['webShop']['cart'][$index]['qty'] += $this->Input->get('articleqty');

			return(true);
    }
    
    protected function findCartItem($id, $vid, $options, $loop = 2) {
      $found = false;
      $index = -1;
      if($loop == 0)
        return(-1);
      
      if(is_array($_SESSION['webShop']['cart'])) {  
        foreach($_SESSION['webShop']['cart'] as $index => $cartItem) {
          $foundAll = true;
          if($cartItem['id'] == $id && $cartItem['vid'] == $vid) {
          	if(!is_array($options) && count($cartItem['options']) == 0) {
	          	$found = true;
	          	break;
          	} else {
          		if(count($options) == count($cartItem['options'])) {
          			foreach($options as $opt) {
          				if(!in_array($opt, $cartItem['options']))
          				  $foundAll = false;
          			}
          		}
          		
          		$found = $foundAll;
          		if($found) {
          			break;
          		}
          	}
          }
        }
      }     
      if($found == false) {
        return($this->addCartItem($id, $vid, $options));
        
      }
      return($index);
    }
    
    protected function addCartItem($id, $vid, $options) {
    	if(!is_array($options)) $options = array($options);
      $_SESSION['webShop']['cart'][] = array(
        'id' => $id,
        'vid' => $vid,
        'qty' => 0,
      	'options' => $options
      );
      return($this->findCartItem($id, $vid, $options));
      
    }
    
    public function __get($key) {
      switch(strtolower($key)) {
      	case 'count': return(count($_SESSION['webShop']['cart'])); break;
      	case 'tax':
        case 'taxes': return($this->arrTaxes); break;
		case 'netto': return($this->totalNetto); break;
		case 'brutto': return($this->totalBrutto); break;
		case 'taxLabel':
        case 'taxesLabel': return(formatPrice($this->totalTax, true)); break;
		case 'nettoLabel': return(formatPrice($this->totalNetto)); break;
        case 'bruttoLabel': return(formatPrice($this->totalBrutto)); break;
		case 'sum': {
			if($this->Tax->showBrutto) {
				return($this->totalNetto + $this->totalTax);
			} else {
				return($this->totalNetto);
			}
		}
		case 'sumtax': return($this->totalTax); break;
        default: {
          if(array_key_exists($key, $this->configuration))
            return($this->configuration[$key]);
          else {
          	die('knonwn key: '. $key);
            return false;
			}
        }
      }
    }
    
		public function __set($key, $value) {
			switch(strtolower($key)) {
				case 'taxes': $this->arrTaxes = $value;
				case 'imageconfig': $this->imageConfig = $value; break;
			}
		}
    
  }

?>
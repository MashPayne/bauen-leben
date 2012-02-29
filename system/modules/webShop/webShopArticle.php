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
 * Class webShopArticle
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopArticle extends Controller {
    
		// storage
		protected $arrItemConfig = array();
		protected $arrVariants = array();
		protected $arrTaxes = array();
		protected $arrJump = array();
		protected $Category;
		protected $imageConfig = array();
		
		// variants
		protected $firstVariantIndex = false;
		protected $selectedVariantID = false;
		
		/* options */
		protected $addPrice = 0;
		protected $addWeight = 0;
		public $arrOptions = array();
	
    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN) {
        $this->Import('FrontendUser', 'User');
      }
			
			if($GLOBALS['TL_CONFIG']['webShop_jumpToArticle']) {
  			$res = $this->Database->prepare('SELECT * from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToArticle']);
				$this->arrJump = $res->fetchAssoc();
			}
			$this->Category = new webShopCategory();
			
      parent::__construct();
    }
    
    		
		protected function loadProductData($arrData) {
			$this->arrItemConfig = $arrData;
			
			$this->Category->load($this->arrItemConfig['pid']);
			return(true);
		}
		
		public function load($id) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_article where id=? AND published=?')->execute($id, 1);
			if($res->numRows == 0)
  			return(false);
			return($this->loadProductData($res->fetchAssoc()));			
		}
				
		
		protected function calcPrice() {
		  /* check for group prices */
      if(strlen($this->arrItemConfig['groupPrices']) && FE_USER_LOGGED_IN) {
        $_groups = deserialize($this->arrItemConfig['groupPrices']);
        $groups = $this->User->groups;
        $arrMatch = array_intersect($_groups['group'], $groups);
        if(is_array($arrMatch)) {
          foreach($arrMatch as $id => $grp)
            $this->arrItemConfig['singlePrice'] = $_groups['value'][$id];
        }
      }
      
      /* check for discount price, overwrites group price*/
		  $this->arrItemConfig['isSpecialPrice'] = false;
      if($this->arrItemConfig['specialprice'] > 0) {
        if(($this->arrItemConfig['specialprice_start'] == '' || $this->arrItemConfig['specialprice_start'] <= time()) && ($this->arrItemConfig['specialprice_stop'] == '' || $this->arrItemConfig['specialprice_stop'] > time())) {
          $sp = $this->arrItemConfig['specialprice'];
          $this->arrItemConfig['isSpecialPrice'] = true;
        } else {
          $sp = $this->arrItemConfig['singlePrice'];
        }
      } else {
        $sp = $this->arrItemConfig['singlePrice'];
      }
      
      $extraPrices = deserialize($this->arrItemConfig['singlePrice2'], true);

      if(count($extraPrices) && strlen($extraPrices[0]['label'])) {
        foreach($extraPrices as $index => $ep) {
          $reach = $ep['value'];
          $newprice = $ep['label'];
          if($this->arrItemConfig['qty'] >= $reach && $newprice < $sp)
            $sp = $newprice;
        }
      }
      
      /* add prices from options */

      $sp += $this->addPrice;
      $this->arrItemConfig['singlePrice'] += $this->addPrice;
      if($this->arrItemConfig['specialprice'] > 0)
	      $this->arrItemConfig['specialprice'] += $this->addPrice;
      
      
      $arrTax = $this->arrTaxes[$this->arrItemConfig['taxid']];
      if($GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
        $netto = $sp / ($arrTax['tax_rate'] / 100 + 1);
        $tax = $sp - $netto;
        $orignetto = $this->arrItemConfig['singlePrice'] / ($arrTax['tax_rate'] / 100 + 1);
        $origtax = $this->arrItemConfig['singlePrice'] - $orignetto;
      } else {
        // Netto preise im Backend
        $netto = $sp;
        $tax = $sp * ($arrTax['tax_rate'] / 100 + 1) - $sp;
        $orignetto = $this->arrItemConfig['singlePrice'];
        $origtax = $this->arrItemConfig['singlePrice'] * ($arrTax['tax_rate'] / 100 + 1) - $orignetto;
      }
      $this->arrItemConfig['orignetto'] = $orignetto;
      $this->arrItemConfig['origtax'] = $origtax;
      
      $this->arrItemConfig['sp_netto'] = $netto;
      
      $this->arrItemConfig['sp_tax'] = $tax;
      
      $this->arrItemConfig['sumNetto'] = $netto * $this->arrItemConfig['qty'];
      $this->arrItemConfig['sumTax'] = $tax * $this->arrItemConfig['qty'];
      
      /* vpe */
      if($this->arrItemConfig['showvpe'] && $this->arrItemConfig['vpeid'] > 0) {
        $this->arrItemConfig['hasvpe'] = true;
        $vpe = $res = $this->Database->prepare('SELECT * from tl_webshop_vpe where id=?')->execute($this->arrItemConfig['vpeid'])->fetchAssoc();
        $this->arrItemConfig['vpeunit'] = $vpe['title'];
        $this->arrItemConfig['vpe_netto'] = $netto / $this->arrItemConfig['vpefactor'];
        $this->arrItemConfig['vpe_tax'] = $tax / $this->arrItemConfig['vpefactor'];
      }
    }

		
		
		protected function generateArticleLink() {
			return($this->generateFrontendUrl($this->arrJump, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $this->Category->alias .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $this->arrItemConfig['alias']));
		}
		
		
    protected function getProductGroupData() {
      $arrRes = array();
      $res = $this->Database->prepare('SELECT t3.* from tl_webshop_article_groups t1, tl_webshop_article t2, tl_webshop_productgroups t3 where t3.id = t1.productgroup AND t1.article=t2.id AND t1.article=?')->execute($this->arrItemConfig['id']);
      if($res->numRows < 1) return($arrRes);
      
	  $arrJump = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCategory'])->fetchAssoc();
      while($res->next()) {
      	$arrRes[] = array('title' => $res->title, 'href' => $this->generateFrontendUrl($arrJump, '/'. $GLOBALS['webShop']['groupKeyword'] .'/'. $res->alias));
      }
      
      return($arrRes);
    }
    
		protected function generateThumb($file) {
			return($this->getImage($file, $this->imageConfig['width'], $this->imageConfig['height']));
		}
		
		public function __get($key) {
			switch(strtolower($key)) {
				case 'dataarray': return($this->arrItemConfig); break;
        case 'price': {
          if($this->arrTaxes[$this->arrItemConfig['taxid']]['showBrutto'])
            return($this->arrItemConfig['sp_netto'] + $this->arrItemConfig['sp_tax']);
          else
            return($this->arrItemConfig['sp_netto']);
        } break;
        case 'singleprice': {
          if($this->arrTaxes[$this->arrItemConfig['taxid']]['showBrutto'])
            return($this->arrItemConfig['orignetto'] + $this->arrItemConfig['origtax']);
          else
            return($this->arrItemConfig['orignetto']);
        } break;
        case 'sum': {
          if($this->arrTaxes[$this->arrItemConfig['taxid']]['showBrutto']) {
            return($this->arrItemConfig['sumNetto'] + $this->arrItemConfig['sumTax']);
          }else
            return($this->arrItemConfig['sumNetto']);
        } break;
				case 'href': return($this->generateArticleLink()); break;
				
				case 'thumb': {
				  if(!strlen($this->arrItemConfig['singleSRC']))
            $this->arrItemConfig['singleSRC'] = $GLOBALS['TL_CONFIG']['webShop_fallBackImage'];
            
				  return($this->generateThumb($this->arrItemConfig['singleSRC'])); 
        } break;
        case 'productgroup': return($this->getProductGroupData()); break;
        case 'specialdata': {
          if($this->arrItemConfig['type'] == 'download') {
            return(array(
              'downloadLimited' => $this->arrItemConfig['downloadLimited'],
              'downloadLifetime' => $this->arrItemConfig['downloadLifetime'] > 0 ?  mktime(date('H'), date('i'), date('s'), date('m'), date('d') + $this->arrItemConfig['downloadLifetime'], date('Y')) : '',
              'downloadCount' => $this->arrItemConfig['downloadCount'] == 0 ? - 1 : $this->arrItemConfig['downloadCount']
            ));
          }
        } break;
        default: {
        	if(array_key_exists($key, $this->arrItemConfig)) {
        		return($this->arrItemConfig[$key]);
        	} else {
        		return('unknown key: '. $key);
        	}
        }
			}
		}
    
		public function __set($key, $value) {
			switch(strtolower($key)) {
				case 'detailpage': $this->arrJump = $value;
	      case 'dataarray': $this->loadProductData($value); break;
				case 'taxes': $this->arrTaxes = $value; $this->calcPrice(); break;
        case 'imageconfig': $this->imageConfig = $value; break;
				default: {
					$this->arrItemConfig[$key] = $value;
				}
			}
		}
		
		public function dataToArray() {
		  return($this->arrItemConfig);
		}
		
		public function isPublished() {
		  $published = true;
		  
		  if($this->arrItemConfig['start'] != '' && $this->arrItemConfig['start'] > time())
		    $published = false;
		    
		  if($this->arrItemConfig['stop'] != '' && $this->arrItemConfig['stop'] < time())
		    $published = false;
		    
		  if($this->arrItemConfig['published'] == '')
		    $published = false;
		    
		  return($published);
		}
    
  }

?>
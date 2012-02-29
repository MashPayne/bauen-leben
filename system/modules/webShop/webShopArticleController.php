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
  * Class webShopArticleController
  *
  * @copyright  Stefan Gandlau 2009
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  */
  
  class webShopArticleController extends Controller {
    
    protected $configuration = array();
    protected $articleid = '';
		protected $objTaxSystem;

    public function generate($cid = false) {
      $this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
      
      $this->articleid = $this->Input->get('article');
			
      // tax controll
      $this->objTaxSystem = new webShopTaxController($this);
      $this->objTaxSystem->generate();
			
      if(strlen($this->articleid)) {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where published=? AND alias=?')->execute('1', $this->articleid);
        if($res->numRows == 0) return;
        $this->configuration = $res->fetchAssoc();
      }
    }
		
		public function compile(&$data) {
			switch($this->configuration['type']) {
				case 'article':
        case 'download': $this->compileArticle($data); break;
				case 'auction': $this->compileAuction($data); break;
				case 'articleVariants': $this->compileArticleVariants($data); break;
			}
		}
		
		protected function compileArticle(&$data) {
			if($this->configuration['addStock']) {
				if($this->configuration['stock'] <= 0 && $this->configuration['hideIfEmpty'])
				  return(false);
			}
			$strTemplate = $data->webShop_articleTemplate;
			if($this->configuration['template'] != $strTemplate)
			  $strTemplate = $this->configuration['template'];
				
			$arrItem = $this->configuration;
      if(strlen($arrItem['groups']) && FE_USER_LOGGED_IN) {
        $_groups = deserialize($arrItem['groups']);
        $groups = $this->User->groups;
        if(!is_array($_groups) || is_array($groups)) return(false);
          if(!array_intersect($_groups, $groups)) return(false);
      }
      
      // group prices
      if(strlen($arrItem['groupPrices']) && FE_USER_LOGGED_IN) {
        $_groups = deserialize($arrItem['groupPrices']);
        $groups = $this->User->groups;
        $arrMatch = array_intersect($_groups['group'], $groups);
        if(is_array($arrMatch)) {
          foreach($arrMatch as $id => $grp)
            $arrItem['singlePrice'] = $_groups['value'][$id];
        }
      }
      
      // tax system
      if(count($this->objTaxSystem->taxes) > 0) {
        if(!$GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
          $arrItem['singlePrice'] += $arrItem['singlePrice'] / 100 * $this->objTaxSystem->taxes[$arrItem['taxid']]['tax_rate'];
        }
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      } else
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      $arrItem['priceLabel'] = number_format($arrItem['singlePrice'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $GLOBALS['TL_CONFIG']['webShop_currencyThausands'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimal']) .' '. $taxLabel;
      
      $data->Template = new Template($strTemplate);				
			$data->Template->title = $arrItem['title'];
			$data->Template->description = $arrItem['description'];
			$data->Template->priceLabel = $arrItem['priceLabel'];
			$data->Template->thumbnail = $this->getImage($arrItem['singleSRC'], $data->thumbWidth, $data->thumbHeight);
			$data->Template->full = $this->getImage($arrItem['singleSRC'], $data->fullWidth, $data->fullHeight);
			$data->Template->articleAttributes = $this->compileSingleAttributes(deserialize($arrItem['attributes']));
		}
		
		protected function compileSingleAttributes($arrAttributes) {
			$arrRes = array();
			if(!is_array($arrAttributes)) return $arrRes;
      $res = $this->Database->prepare('SELECT t1.title as catname, t2.title as valuename from tl_webshop_attributecategories as t1, tl_webshop_attributes as t2 where t1.id = t2.pid AND t1.id IN ('. join(',', array_keys($arrAttributes)) .') AND t2.id IN ('. join(',', array_values($arrAttributes)) .') ORDER by t1.title')->execute();
			if($res->numRows == '') return;
			while($res->next()) {
				$arrRes[] = sprintf('%s: %s', $res->catname, $res->valuename);
			}
			return(join('<br/>', $arrRes));
		}
		
		protected function compileArticleVariants(&$data, $variant = 0) {
			// get all variants
			$arrVariants = array();
			$res = $this->Database->prepare('SELECT * from tl_webshop_articlevariants where published=? AND pid=? AND (addStock = "" || ((addStock = "1" AND stock > 0) || (addStock = "1" AND stock <= 0 AND hideIfEmpty = ""))) order by sorting')->execute(1, $this->configuration['id']);
			if($res->numRows == 0) return(false);
			while($res->next()) {
				$arrVariants[] = $res->row();
			}
			$currentVariant = $arrVariants[$variant];
			
			// pasted stuff
			$strTemplate = $data->webShop_articleTemplate;
      if($this->configuration['template'] != $strTemplate)
        $strTemplate = $this->configuration['template'];
        
      $arrItem = $this->configuration;
      if(strlen($arrItem['groups']) && FE_USER_LOGGED_IN) {
        $_groups = deserialize($arrItem['groups']);
        $groups = $this->User->groups;
        if(!is_array($_groups) || is_array($groups)) return(false);
          if(!array_intersect($_groups, $groups)) return(false);
      }
      
			$arrItem['singlePrice'] = $currentVariant['singlePrice'];
      // group prices
      if(strlen($currentVariant['groupPrices']) && FE_USER_LOGGED_IN) {
        $_groups = deserialize($currentVariant['groupPrices']);
        $groups = $this->User->groups;
        $arrMatch = array_intersect($_groups['group'], $groups);
        if(is_array($arrMatch)) {
          foreach($arrMatch as $id => $grp)
            $arrItem['singlePrice'] = $_groups['value'][$id];
        }
      }
      
      // tax system
      if(count($this->objTaxSystem->taxes) > 0) {
        if(!$GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
          $arrItem['singlePrice'] += $arrItem['singlePrice'] / 100 * $this->objTaxSystem->taxes[$arrItem['taxid']]['tax_rate'];
        }
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      } else
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      $arrItem['priceLabel'] = number_format($arrItem['singlePrice'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $GLOBALS['TL_CONFIG']['webShop_currencyThausands'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimal']) .' '. $taxLabel;
      
      $data->Template = new Template($strTemplate);       
      $data->Template->title = $arrItem['title'];
      $data->Template->description = $arrItem['description'];
      $data->Template->priceLabel = $arrItem['priceLabel'];
      $data->Template->thumbnail = $this->getImage($arrItem['singleSRC'], $data->thumbWidth, $data->thumbHeight);
      $data->Template->full = $this->getImage($arrItem['singleSRC'], $data->fullWidth, $data->fullHeight);
      $data->Template->articleAttributes = $this->compileMultiAttributes($arrVariants, $currentVariant);
		}
		
		protected function compileMultiAttributes($arrVariants, $currentVariant) {
			$arrAttributes = deserialize($currentVariant['attributes']);
			
			$arrCombined = array();
			foreach($arrVariants as $id => $variant) {
				$attributes = deserialize($variant['attributes']);
				foreach($attributes as $cat => $item) {
					if(!is_array($arrCombined[$cat]))
					  $arrCombined[$cat] = array();
						
				  if(!in_array($item, $arrCombined[$cat]))
  				  $arrCombined[$cat][] = $item;
				}
			}
			
			// get labels
			$res = $this->Database->prepare('SELECT * from tl_webshop_attributecategories')->execute();
			while($res->next())
			  $arrCatLabel[$res->id] = $res->title;
			$res = $this->Database->prepare('SELECT * from tl_webshop_attributes')->execute();
			while($res->next())
			  $arrItemLabel[$res->id] = $res->title;
			
			// build html
			foreach($arrCombined as $category => $items) {
				$html .= sprintf('<label for="ctrl_attributes_%s">%s</label><br/>', $category, $arrCatLabel[$category]);
				$html .= sprintf('<select id="attr[%s]" name="attr[%s]"%s>', $category, $category, count($items) == 1 ? ' disabled="disabled"' : '');
				foreach($items as $item)
				  $html .= sprintf('<option value="%s"%s>%s</option>', $item, $arrAttributes[$category] == $item ? ' selected="selected"' : '', $arrItemLabel[$item]);
			  $html .= '</select><br/>';
			}
			
			return($html);
		}
        
    protected function compileAuction(&$data) {
      $data->Template->title = 'not implemented';
    }
		
    public function isAllowed($arrGroups) {
      $cGroups = deserialize($this->configuration['groups']);
      if(is_array($cGroups) && is_array($arrGroups))
        if(array_intersect($arrGroups, $cGroups))
          return true;
        
      return false;
    }
    
    public function __get($strKey) {
      if(array_key_exists($strKey, $this->configuration))
        return($this->configuration[$strKey]);
        
      return '';
    }

    public function __set($strKey, $strVal) {
      $this->configuration[$strKey] = $strVal;
    }
    
  }

?>
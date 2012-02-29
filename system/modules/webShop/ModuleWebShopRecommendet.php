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
 * Class ModuleWebShopRecommendet
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleWebShopRecommendet extends Module {
    
    protected $strTemplate = 'webShop_recommendet';
    protected $thumbWidth = 0;
		protected $thumbHeight = 0;
		
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### RECOMMENDET ARTICLE ###';
        return($t->parse());
      }
			
			if(FE_USER_LOGGED_IN)
			  $this->Import('FrontendUser', 'User');
				
      $this->objCategory = new webShopCategoryController($this);
      $this->objCategory->generate();
			
			$this->Import('webShopTaxController', 'Tax');
			
			
      // image config
      if(!strlen($this->webShop_thumbSize)) {
        $this->thumbWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
        $this->thumbHeight = false;
      } else {
        $arrSize = deserialize($this->webShop_thumbSize);
        $this->thumbWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->thumbHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }

      return(parent::generate());
    }
    
    protected function compile() {
    	$arrItems = array();
      	$limit = $this->webShop_limit;	
		  if($limit == 0) $limit = 999;
			
      $article = $this->Input->get($GLOBALS['webShop']['articleKeyword']);
      $res = $this->Database->prepare('SELECT * from tl_webshop_article where alias=? AND published=? AND recommendet!=""')->execute($article, 1);
			if($res->numRows == 0) return;
			
			$arrArticle = $res->fetchAssoc();
			$linked = deserialize($arrArticle['recommendet'], true);
			if(count($linked) == 0) return;
      shuffle($linked);
			
      foreach($linked as $id) {
      	$limit--;
      	$objArticle = new webShopArticle();
				$objArticle->imageConfig = array('width' => $this->thumbWidth, 'height' => $this->thumbHeight);
				$r = $objArticle->load($id);
				if($r == false) continue;
				$arrItems[] = $objArticle;

				
				if($limit == 0) break; // limit reached, break loop
      }
			
			
      // fill template
			$this->Template->show = true;
			
			$this->Template->items = $arrItems;			
    }
		
		
    
  }

?>
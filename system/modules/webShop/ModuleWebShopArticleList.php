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
 * Class ModuleWebShopArticleList
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  require_once('functions.php');

  class ModuleWebShopArticleList extends Module {
    
    protected $strTemplate = 'webShop_articlelist_default';
    protected $objCategory;
    protected $objTaxSystem;
    protected $arrJump;
    protected $arrLocal = array();
    protected $thumbWidth;
    protected $thumbHeight;
    protected $categoryImageWidth;
    protected $categoryImageHeight;
    protected $pageShipping = array();
    protected $arrGroups = array();
    
    protected $webShop_numArticles;
    
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### WEBSHOP ARTICLELIST MODULE ###';
        return($t->parse());
      }
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
      
      /* current category */
      $this->objCategory = new webShopCategoryController($this);
      $this->objCategory->generate();
      
      /* tax controll */
      $this->Import('webShopTaxController', 'objTaxSystem');      
      /* detail page */
      $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
      $this->arrJump = $res->fetchAssoc();
      
      /* this page, for productgroup listing */
      global $objPage;
      $this->arrLocal = array('id' => $objPage->id, 'alias' => $objPage->alias);
      
      /* productgroups */
      $res = $this->Database->prepare('SELECT * from tl_webshop_productgroups')->execute();
      if($res->numRows > 0) {
        while($res->next()) {
          $this->arrGroups[$res->id] = $res->row();
        }
      }
      
      /* image config */
      if(!strlen($this->webShop_thumbSize)) {
        $this->thumbWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
        $this->thumbHeight = false;
      } else {
        $arrSize = deserialize($this->webShop_thumbSize);
        $this->thumbWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->thumbHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }
      
      if(!strlen($this->webShop_categoryImageSize)) {
        $this->categoryImageWidth = false;
        $this->categoryImageHeight = false;
      } else {
        $arrSize = deserialize($this->webShop_categoryImageSize);
        $this->categoryImageWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->categoryImageHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }
      
      $this->webShop_numArticles = $this->perPage;
    	if($this->Input->post('FORM_ACTION') == 'webShopAddCartItem') {
			$objCart = new webShopShoppingCart();
			$res = $objCart->addItem();
			if(!$res) {
				die('ERROR');
				$this->error = $GLOBALS['TL_LANG']['webShop']['error_addCartItem'];
			} else {
				$_SESSION['webShop']['itemAdded'] = true;
				$this->reload();
			}
		}
      return(parent::generate());
    }
    
    protected function compile() {

    	if($this->webShop_articleListTemplate)
    	  $this->Template = new FrontendTemplate($this->webShop_articleListTemplate);
      if($GLOBALS['TL_CONFIG']['webShop_jumpToShipping']) {
        $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToShipping']);
        $this->pageShipping = $res->fetchAssoc();
      }

      if($this->Input->get($GLOBALS['webShop']['categoryKeyword']) == '') {
      if($this->Input->get('tag') != '') {
		
      	$this->Template->items = $this->getTaglistArticle($this->Input->get('tag'));
      	return;
      }
        $productGroup = $this->Input->get($GLOBALS['webShop']['groupKeyword']);
        $res = $this->Database->prepare('SELECT * from tl_webshop_productgroups where alias=?')->execute($productGroup);
        if($res->numRows == 0)
          return;
        
        
        $this->Template->items = $this->getProductGroupArticles($res->fetchAssoc());
        return;
      } 
      
      
      if($this->objCategory->protected == '1') {
        if(!BE_USER_LOGGED_IN) {
          if(!FE_USER_LOGGED_IN) {
            $objHandler = new $GLOBALS['TL_PTY']['error_403']();
            $objHandler->generate($this->getRootIdFromUrl());
            return;
          } else {
            if(!$this->objCategory->isAllowed(deserialize($this->User->groups))) {
              $objHandler = new $GLOBALS['TL_PTY']['error_403']();
              $objHandler->generate($this->getRootIdFromUrl());
              return;
            }
          }
        }
      }
      
      $parentCategory = $this->objCategory->parentCategory;

      $this->Template->headline = $this->objCategory->title;
      
      /* get global page object */
      global $objPage;
      
      /* set page title */
      if($this->objCategory->pagetitle != '')
          $objPage->pageTitle = $this->objCategory->pagetitle;
      else
	      $objPage->pageTitle = $this->objCategory->title;
      $this->Template->title = $this->objCategory->title;
      /* set keywords */
      if(strlen($this->objCategory->categoryKeywords))
        $GLOBALS['TL_KEYWORDS'] = $this->objCategory->categoryKeywords;
      
      /* set description */
      if(strlen($this->objCategory->categoryDescription)) {
        $objPage->description = strip_tags($this->objCategory->categoryDescription);
      }
      
      /* show teaser if enabled and filled */
      if($this->objCategory->showTeaser && strlen($this->objCategory->teaser)) {
        $this->Template->teaser = $this->objCategory->teaser;
      }
      
      /* show sub-categories */
      if($this->objCategory->showCategories) {
        if(FE_USER_LOGGED_IN)
          $arrUserGroups = deserialize($this->User->groups);
          
        $arrSub = array();
        $objCategoryTemplate = new FrontendTemplate($this->objCategory->categoryTemplate);
        $arrSubcategors = $this->objCategory->getSubCategories();
        if(is_array($arrSubcategors)) {
          foreach($arrSubcategors as $subcat) {
            
            /* skip if categories is hidden */
            if($subcat['hide'])
              continue;
              
            /* check if categorie is protected */
            if($subcat['protected'] == '1') {
             /* skip if no frontend user is logged in */
              if(!FE_USER_LOGGED_IN) continue;
              
              /* skip if no groups selected */
              if(!strlen($subcat['groups'])) continue;
              
              /* skip if frontend user not in group */
              if(!array_intersect($arrUserGroups, deserialize($subcat['groups']))) continue;
            }
            
            $arrSub[] = array(
              'href' => $this->generateFrontendUrl(array('id' => $objPage->id, 'alias' => $objPage->alias), '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $subcat['alias']),
              'title' => $subcat['title'],
              'img' => $subcat['categoryImage'] ? $this->getImage($subcat['categoryImage'], $this->categoryImageWidth, $this->categoryImageHeight) : false
            );
          }
          if(count($arrSub) > 0) {
            $objCategoryTemplate->categories = $arrSub;
            $this->Template->subcategories = $objCategoryTemplate->parse();
          }
        }
      }
      if($this->objCategory->sortable) {
        $arrSort = array('default', 'name', 'name-desc', 'price', 'price-desc');
        foreach($arrSort as $index => $sort) {
          $arrSort[$index] = array('selected' => $this->Input->get('sort') == $sort ? true : false, 'href' => $this->generateFrontendUrl($this->arrLocal, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $this->objCategory->alias .'/sort/'. $sort), 'title' => $GLOBALS['TL_LANG']['webShop']['sortings'][$sort]);
        }
        $frmSort = new FrontendTemplate('ws_sorting');
        $frmSort->links = $arrSort;
        $frmSort->lblSorting = $GLOBALS['TL_LANG']['webShop']['lbl_sorting'];
        $this->Template->sortbox = $frmSort->parse();
      }
      $this->Template->items = $this->getCategoryArticles();
    }
    
    protected function getTaglistArticle($tag) {
		$offset = $this->Input->get('page');
	    if($offset == '') $offset = 1;
	      $offset = ($offset - 1) * $this->webShop_numArticles;
	      $limit = $this->webShop_numArticles;
	      
	    /* get all article */
	      
	    $res = $this->Database->prepare('select t1.* from tl_webshop_article t1 where t1.published=? AND t1.tags REGEXP \'[[:<:]]'. $tag .'[[:>:]]\' AND (t1.addStock = "" || ((t1.addStock = "1" && t1.stock > 0) || (t1.addStock = "1" && t1.stock <= 0 && t1.hideIfEmpty = ""))) AND (t1.start <= ? OR t1.start = "") AND (t1.stop > ? OR t1.stop = "") ORDER BY t1.sorting')->execute('1', time(), time());
	    if($res->numRows == 0) return array();
	    $intAll = $res->numRows;
	    
	    /* get article according to limit */
	    
	    if($limit > 0) {
	      $res = $this->Database->prepare('select t1.* from tl_webshop_article t1 where t1.published=? AND t1.tags REGEXP \'[[:<:]]'. $tag .'[[:>:]]\' AND (t1.addStock = "" || ((t1.addStock = "1" && t1.stock > 0) || (t1.addStock = "1" && t1.stock <= 0 && t1.hideIfEmpty = ""))) AND (t1.start <= ? OR t1.start = "") AND (t1.stop > ? OR t1.stop = "") ORDER BY t1.sorting LIMIT '. $offset .','. $limit)->execute('1', time(), time());
	      $objPagination = new Pagination($intAll, $limit);
	      $this->Template->pagination = $objPagination->generate();
	    }
	    
	    global $objPage;
	    $this->Template->headline = $tag;
	    while($res->next()) {
	      $arrD = $res->row();
	      $this->Template->title = $arrD['gtitle'];
	      $item = $this->compileArticleData($arrD);
	      if($item != false)
	        $arrItems[] = $item;
	    }
	    return($arrItems);
    }
    
    
  protected function getProductGroupArticles($grp) {
    $offset = $this->Input->get('page');
    if($offset == '') $offset = 1;
      $offset = ($offset - 1) * $this->webShop_numArticles;
      $limit = $this->webShop_numArticles;
    /* get all article */
    $res = $this->Database->prepare('select t1.*, t2.title as gtitle from tl_webshop_article t1, tl_webshop_productgroups t2, tl_webshop_article_groups t3 where t3.productgroup=t2.id AND t1.published=? AND t3.productgroup=? AND t1.id = t3.article AND (t1.addStock = "" || ((t1.addStock = "1" && t1.stock > 0) || (t1.addStock = "1" && t1.stock <= 0 && t1.hideIfEmpty = ""))) AND (t1.start <= ? OR t1.start = "") AND (t1.stop > ? OR t1.stop = "") ORDER BY t1.sorting')->execute('1', $grp['id'], time(), time());
    if($res->numRows == 0) return array();
    $intAll = $res->numRows;
    
    /* get article according to limit */
    
    if($limit > 0) {
      $res = $this->Database->prepare('select t1.*, t2.title as gtitle from tl_webshop_article t1, tl_webshop_productgroups t2, tl_webshop_article_groups t3 where t3.productgroup=t2.id AND t1.published=? AND t3.productgroup=? AND t1.id = t3.article AND (t1.addStock = "" || ((t1.addStock = "1" && t1.stock > 0) || (t1.addStock = "1" && t1.stock <= 0 && t1.hideIfEmpty = ""))) AND (t1.start <= ? OR t1.start = "") AND (t1.stop > ? OR t1.stop = "") ORDER BY t1.sorting LIMIT '. $offset .','. $limit)->execute('1', $grp['id'], time(), time());
      $objPagination = new Pagination($intAll, $limit);
      $this->Template->pagination = $objPagination->generate();
    }
    
    $GLOBALS['TL_KEYWORDS'] = $grp['keywords'];
    global $objPage;
    $objPage->description = $grp['description'];
    $this->Template->headline = $grp['title'];
    while($res->next()) {
      $arrD = $res->row();
      $this->Template->title = $arrD['gtitle'];
      $item = $this->compileArticleData($arrD);
      if($item != false)
        $arrItems[] = $item;
    }
    global $objPage;
    if($grp['pagetitle'])
      $objPage->pageTitle = $grp['pagetitle'];
    else
      $objPage->pageTitle = $grp['title'];
      
    if(strlen($grp['descriptiontext'])) {
        $this->Template->teaser = $grp['descriptiontext'];
      }
    return($arrItems);
  }
    
  protected function getCategoryArticles() {
      $arrItems = array();
      if($this->objCategory->id == '') return array();
      
      $offset = $this->Input->get('page');
      if($offset == '') $offset = 1;
      $offset = ($offset - 1) * $this->webShop_numArticles;
      $limit = $this->webShop_numArticles;
      /* get all article */
      if($this->objCategory->type == 'category') {
        $res = $this->Database->prepare('select t1.* from tl_webshop_article t1 where t1.published=? AND t1.pid=? AND (t1.addStock = "" || ((t1.addStock = "1" && t1.stock > 0) || (t1.addStock = "1" && t1.stock <= 0 && t1.hideIfEmpty = ""))) AND (t1.start <= ? OR t1.start = "") AND (t1.stop > ? OR t1.stop = "") ORDER BY t1.sorting')->execute('1', $this->objCategory->id, time(), time());
      } elseif($this->objCategory->type == 'latest') {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where published=? AND (addStock = "" || ((addStock = "1" && stock > 0) || (addStock = "1" && stock <= 0 && hideIfEmpty = ""))) AND (start <= ? OR start = "") AND (stop > ? OR stop = "") ORDER BY added desc')->execute(1, time(), time());
      } elseif($this->objCategory->type == 'newMarked') {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where published=? AND isnew=1 AND (addStock = "" || ((addStock = "1" && stock > 0) || (addStock = "1" && stock <= 0 && hideIfEmpty = ""))) AND (start <= ? OR start = "") AND (stop > ? OR stop = "") ORDER BY added desc')->execute(1, time(), time());
      } elseif($this->objCategory->type == 'offerMarked') {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where published=? AND specialoffer=1 AND (addStock = "" || ((addStock = "1" && stock > 0) || (addStock = "1" && stock <= 0 && hideIfEmpty = ""))) AND (start <= ? OR start = "") AND (stop > ? OR stop = "") ORDER BY added desc')->execute(1, time(), time());
      }
      if($res->numRows == 0) return array();
      $intAll = $res->numRows;
      
      /* get article according to limit */
      
      
      while($res->next()) {
        $item = $res->row();
        if($item != false)
          $arrItems[] = $this->prepareArticleData($item);
      }

      if($limit > 0) {
        $objPagination = new Pagination($intAll, $limit);
        $this->Template->pagination = $objPagination->generate();
        
        $arrItems = array_slice($arrItems, $offset, $limit);
      }
      
      
      /* article sorting */
      if($this->objCategory->sortable) {
        $sorting = $this->Input->get('sort');
        if(!strlen($sorting)) $sorting = 'default';
        
        switch($sorting) {
	        case 'name': $arrItems = $this->sortByField($arrItems, 'title'); break;
	        case 'name-desc': $arrItems = $this->sortByField($arrItems, 'title', true); break;
	        case 'price': $arrItems = $this->sortByField($arrItems, 'singlePrice_sort'); break;
	        case 'price-desc': $arrItems = $this->sortByField($arrItems, 'singlePrice_sort', true); break;
	        default: $arrItems = $this->sortByField($arrItems, 'sorting');
        }
      }
      
      foreach($arrItems as $index => $item)
        $arrItems[$index] = $this->compileArticleData($item);
        
      return($arrItems);
    }
    
    protected function sortByField($arrItems, $strField, $reverse = false) {
      $tmp = array();
      foreach($arrItems as &$i)
        $tmp[] = &$i[$strField];
        
      array_multisort($tmp, $arrItems);
      
      if($reverse)
        return(array_reverse($arrItems));
        
      return($arrItems);
    }
    
    protected function prepareArticleData($arrItem) {
      $objItem = new webShopArticle();
      $objItem->dataArray = $arrItem;
      $objItem->qty = 1;

          
      $objItem->taxes = $this->objTaxSystem->taxes;
      
      $arrItem['singlePrice_sort'] = $objItem->isSpecialPrice ? $objItem->price : $objItem->singleprice;
      
      return($arrItem);
    }
    
    protected function compileArticleData($arrItem) {
      $objTemplate = new FrontendTemplate($this->webShop_articleListItemTemplate);
      
      /* check if this is a forwarded article */
      if($arrItem['type'] == 'forward') {
        if($arrItem['linkTarget'] > 0) {
          $res = $this->Database->prepare('SELECT * from tl_webshop_article where id=?')->execute($arrItem['linkTarget']);
          if($res->numRows == 0)
            return(false);
          else
            return($this->compileArticleData($res->fetchAssoc()));
        } else
          return(false);        
      }
      
      /* check credentials */
      if(strlen($arrItem['groups'])) {
        if(!FE_USER_LOGGED_IN) return(false);
        
        $_groups = deserialize($arrItem['groups']);
        $groups = $this->User->groups;
        if(!is_array($_groups) || !is_array($groups)) return(false);
        if(!array_intersect($_groups, $groups)) return(false);
      }
      
      $objArticle = new webShopArticle();
      $objArticle->detailpage = $this->arrJump;
      $objArticle->imageConfig = array('width' => $this->thumbWidth, 'height' => $this->thumbHeight);
      $objArticle->dataArray = $arrItem;
      $objArticle->qty = 1;
      
      $objArticle->taxes = $this->objTaxSystem->taxes;
      /* productgroup link */
      $pGroup = $objArticle->productgroup;
      $objTemplate->lblProductGroup = $pGroup['title'];
      $objTemplate->lnkProductGroup = $pGroup['href'];
      
      /* fill template */
      $objTemplate->title = $objArticle->title;
      $objTemplate->prices = $this->createPriceBlock($objArticle->price, $objArticle->singleprice, $objArticle->isSpecialPrice, $objArticle->dataarray);
      
      /* tax label and shipping notice */
      $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      /* brutto? add Taxes and change label */
      if($this->objTaxSystem->showBrutto) {
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      }
      $objTemplate->taxLabel = $taxLabel;
      $objTemplate->shippingNotice = sprintf($GLOBALS['TL_LANG']['webShop']['FE_LABEL']['shippingNoticeEx'], $this->generateFrontendUrl($this->pageShipping));
      
      $objTemplate->href = $objArticle->href;
      $objTemplate->thumbnail = $objArticle->thumb;
      $objTemplate->full = $objArticle->singleSRC;
      $objTemplate->articleid = $objArticle->id;
      $objTemplate->articlenumber = $objArticle->productid;
      
      if(strlen($arrItem['isnew'])) {
        $objTemplate->markAsNew = $GLOBALS['TL_CONFIG']['webShop_markAsNew'];
      }
      if(strlen($arrItem['specialoffer'])) {
        $objTemplate->markAsOffer = $GLOBALS['TL_CONFIG']['webShop_markAsOffer'];
      }
      
      if($arrItem['teaser'])
        $objTemplate->teaser = $arrItem['teaser'];
       
      $objTemplate->cssClass = $arrItem['type'];
      
      return($objTemplate->parse());

    }
    
    protected function createPriceBlock($price, $singlePrice, $isSpecialPrice, $arrArticle) {
    
      $objT = new FrontendTemplate('webShop_priceLabel');
      $objT->isSpecialPrice = $isSpecialPrice;
      $objT->price = $price;
      $objT->singlePrice = $singlePrice;

      /* tax label and shipping notice */
      $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      /* brutto? add Taxes and change label */
      if($this->objTaxSystem->showBrutto) {
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      }
      $objT->taxLabel = $taxLabel;
      $objT->shippingNotice = sprintf($GLOBALS['TL_LANG']['webShop']['FE_LABEL']['shippingNoticeEx'], $this->generateFrontendUrl($this->pageShipping));
      if($arrArticle['hasvpe']) {
        $objT->hasvpe = true;
        if($this->objTaxSystem->showBrutto)
          $objT->vpe = $arrArticle['vpe_netto'] + $arrArticle['vpe_tax'];
        else
          $objT->vpe = $arrArticle['vpe_netto'];
          
        $objT->vpeunit = $arrArticle['vpeunit'];
      }
      return($objT->parse());
    }
    
  }

?>
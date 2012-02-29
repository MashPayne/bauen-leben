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
 * Class ModuleWebShopArticleDetails
 *
 * @copyright  Stefan Gandlau 2009-2012
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  require_once('functions.php'); 

  class ModuleWebShopArticleDetails extends Module {
    
    protected $strTemplate = 'webShop_articledetails_default';
    protected $objCategory;
		protected $objAjax;
		protected $formKey = '';
		protected $arrArticle = array();
		protected $gallery = array();
		protected $gallersXML = '';
		protected $objGallery = NULL;
		protected $hasOptions = false;
		
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### WEBSHOP ARTICLE DETAILS ###';
        return($t->parse());
      }

      $this->Import('Config');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
		
      // current category
      $this->objCategory = new webShopCategoryController($this);
      $this->objCategory->generate();
      
			$this->objAjax = new webShopAjaxController($this);
			
			// tax controll
      $this->Import('webShopTaxController', 'objTaxSystem');
			
      // detail page
      $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
      $this->arrJump = $res->fetchAssoc();
      
      // image config
      if(!strlen($this->webShop_thumbSize)) {
        $this->thumbWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
        $this->thumbHeight = false;
      } else {
        $arrSize = deserialize($this->webShop_thumbSize, true);
        $this->thumbWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->thumbHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }
      
      if(!strlen($this->webShop_fullSize)) {
        $this->fullWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
        $this->fullHeight = false;
      } else {
        $arrSize = deserialize($this->webShop_fullSize, true);
        $this->fullWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->fullHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }
      
      if(!strlen($this->webShop_miniSize)) {
        $this->miniWidth = false; //$GLOBALS['TL_CONFIG']['maxImageWidth'];
        $this->miniHeight = 80;
      } else {
        $arrSize = deserialize($this->webShop_miniSize, true);
        $this->miniWidth = $arrSize[0] != '' ? $arrSize[0] : false;
        $this->miniHeight = $arrSize[1] != '' ? $arrSize[1] : false;
      }
      
      

      
      if($GLOBALS['TL_CONFIG']['webShop_jumpToShipping']) {
        $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToShipping']);
        $this->pageShipping = $res->fetchAssoc();
      }
      


			if($this->Input->get('FORM_ACTION') == 'webShopAjax') {
				$res = $this->Database->prepare('SELECT * from tl_webshop_article where alias=? AND (start = "" OR start <= ?) AND (stop >= ? OR stop = "")')->execute($this->Input->get($GLOBALS['webShop']['articleKeyword']), time(), time());
				if($res->numRows == 0) {
					$this->objAjax->addAction('alert', '', 'Ung&uuml;ltiger aufruf!');
				} else {
					$this->arrArticle = $res->fetchAssoc();
					// do ajax actions and exit
					
				}
				header('Content-Type: text/xml; charset=UTF-8');
				die(trim($this->objAjax->buildAjaxXML()));
			}
      return(parent::generate());
    }
    
    protected function compile() {
    	$this->Template = new FrontendTemplate($this->webShop_articleTemplate);
    	
    	if($this->objCategory->protected == '1') {
    	  if(!BE_USER_LOGGED_IN) {
          if(!FE_USER_LOGGED_IN) {
            $objHandler = new $GLOBALS['TL_PTY']['error_403']();
            $objHandler->generate($pageId);
            return;
          } else {
            if(!$this->objCategory->isAllowed(deserialize($this->User->groups))) {
              $objHandler = new $GLOBALS['TL_PTY']['error_403']();
              $objHandler->generate($pageId);
              return;
            }
          }
        }
      }
      $GLOBALS['TL_JAVASCRIPT']['webShopAjax'] = 'system/modules/webShop/html/webShopAjax.js';
      $GLOBALS['TL_JAVASCRIPT']['elFlash'] = 'system/modules/webShop/html/elFlash.js';
      global $objPage;
      
	  $article = $this->Input->get($GLOBALS['webShop']['articleKeyword']);
	  if(strlen($article))
  	  $arrArticle = $this->compileArticle($article);
      
    if($this->Input->get('FORM_ACTION') == 'webShopAddCartItem') {
				$isAjax = $this->Input->get('isAjax');
       	 		$article = $this->arrArticle['id'];
				$objCart = new webShopShoppingCart();
				$res = $objCart->addItem();
				if($isAjax) {
					if(!$res) {
						die(json_encode(array('status' => 1, 'message' => $GLOBALS['TL_LANG']['webShop']['error_addCartItem'])));
					} else {
						$objT = new FrontendTemplate('webShop_minicart');
						$objCart->taxes = $this->objTaxSystem->taxes;
						$objCart->getItems();
						if($objCart->Count  > 0) $objT->cartActive = 'cart_active ';
						$objT->itemCount = $objCart->count;
						if($this->objTaxSystem->showBrutto)
					        $cartPrice = $objCart->brutto;
					    else
					        $cartPrice = $objCart->netto;
					    $objT->cartPrice = $cartPrice;
					    $objT->linkCart = $this->generateFrontendUrl($this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCart'])->fetchAssoc());
					    $this->objAjax->addAction('setByClass', '.mod_webShop_miniCart', $objT->parse());
					    $this->objAjax->addAction('toggleVisibility', 'lbl_itemAdded', 'block');
					    die($this->objAjax->send());
//						die(json_encode(array('status' => 0, 'update' => 'cart', 'cartHTML' => $objT->parse())));
					}
				} else {
					if(!$res) {
						$this->error = $GLOBALS['TL_LANG']['webShop']['error_addCartItem'];
					} else {
						$_SESSION['webShop']['itemAdded'] = true;
						$this->redirect($this->generateFrontendUrl(array('id' => $objPage->id, 'alias' => $objPage->alias), '/kategorie/'. $arrArticle['category_alias'] .'/artikel/'. $arrArticle['alias']));
					}
				}
			}
  	  
		
		
		
	  if(!$arrArticle) {
	  	  $objHandler = new $GLOBALS['TL_PTY']['error_404']();
		  $objHandler->generate($this->getRootIdFromUrl());
	  }
			

			$this->Template->articleid = $arrArticle['id'];
			$this->Template->title = $arrArticle['title'];
			$this->Template->teaser = $arrArticle['teaser'];
			$this->Template->productid = $arrArticle['productid'];
			$this->Template->text = $arrArticle['description'];
			$this->Template->tabtext = $this->getArticleTabs($arrArticle);
			$this->Template->image = $arrArticle['arrImage'];
			
			
			
			
			if(strlen($arrArticle['isnew']))
				$this->Template->markAsNew = 1;
			if(strlen($arrArticle['specialoffer']))
			    $this->Template->markAsOffer = 1;
			    
			  $this->Template->markAsOfferImage = $GLOBALS['TL_CONFIG']['webShop_markAsOffer'];
			  $this->Template->markAsNewImage = $GLOBALS['TL_CONFIG']['webShop_markAsNew'];
				 
			
			$this->Template->delivery = $arrArticle['deliveryTime'];
			$this->Template->arrData = $arrArticle;
      		$this->Template->isSample = $arrArticle['sampleImage'];
      
      		$this->Template->prices = $this->createPriceBlock($arrArticle);
      
   		$arrBlockPrices = deserialize($arrArticle['singlePrice2'], true);
   		if(count($arrBlockPrices) > 0 && $arrBlockPrices[0]['value'] != '')
   		  $this->Template->blockPrices = $this->compileBlockpricesList($arrBlockPrices, $arrArticle['taxid']);
   		
   		$this->Template->productgroups = $arrArticle['productgroups'];

      
      $arrPrices2 = deserialize($arrArticle['singlePrice2'], true);
      if(is_array($arrPrices2) && count($arrPrices2) > 0) {
        $this->Template->blockprices = $this->compileBlockpricesList($arrPrices2, $arrArticle['taxid']);
      }

      /*
       * Gallery
       */
      
      $arrImages = array();
      if(strlen($arrArticle['singleSRC']))
        $arrImages[] = $arrArticle['singleSRC'];
      else
        $arrImages[] = $GLOBALS['TL_CONFIG']['webShop_fallBackImage'];
        
      
      if($arrArticle['addGallery'] && strlen($arrArticle['multiSRC'])) {
        $arrImages = $this->collectGalleryImages(deserialize($arrArticle['multiSRC'], true), $arrImages);
      }
      
      foreach($arrImages as $index => $image) {
      	$arrImages[$index] = array(
      		'thumb' => $this->getImage($image, $this->thumbWidth, $this->thumbHeight),
      		'full' => $this->getImage($image, $this->fullWidth, $this->fullHeight),
      		'mini' => $this->getImage($image, $this->miniWidth, $this->miniHeight),
      	    'orig' => $image
      	);
      }
      
      if(count($arrImages) > 1) {
      	$GLOBALS['TL_JAVASCRIPT']['imageslider'] = 'system/modules/webShop/html/imageslide.js';
      	$GLOBALS['TL_CSS']['imageslide'] = 'system/modules/webShop/html/imageslide.css';
      	$this->Template->showSlider = true;
      	$this->Template->arrImages = $arrImages;
      }

	
      if($this->wsGallery == 'mojozoom') {
      	$GLOBALS['TL_CSS']['mojozoom'] = 'system/modules/webShop/html/mojozoom.css';
	    $GLOBALS['TL_JAVASCRIPT']['mojozoom'] = 'system/modules/webShop/html/mojozoom.js';
      	
      }
      
      $this->Template->galleryType = $this->wsGallery;
	  $this->Template->url = $this->Environment->base . $this->Environment->request;
      
      
			$objPage->pageTitle = $arrArticle['title'];
			if(strlen($arrArticle['keywords']))
			  $GLOBALS['TL_KEYWORDS'] = $arrArticle['keywords'];
			if(strlen($arrArticle['seoDescription'])) {
				$objPage->description = str_replace("\n", " ", $arrArticle['seoDescription']);
			}
						
			
			$this->Template->data = $arrArticle;
			$this->Template->formKey = $this->formKey;
			$this->Template->lbl_addToCart = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['addToCart'];
			$this->Template->href = $this->generateFrontendUrl(array('id' => $objPage->id, 'alias' => $objPage->alias), '/'. $GLOBALS['webShop']['categoryKeyword']. '/'. $this->objCategory->alias .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $arrArticle['alias']);
			$this->Template->itemAddedText = sprintf($GLOBALS['TL_LANG']['webShop']['itemAdded'], ($this->generateFrontendUrl($this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCart'])->fetchAssoc())));
			$this->Template->itemAdded = $_SESSION['webShop']['itemAdded'] ? true : false;
			
			if($_SESSION['webShop']['itemAdded']) {
				unset($_SESSION['webShop']['itemAdded']);
			}
			if(strlen($this->error))
			  $this->Template->error = $this->error;
			  
		$this->Template->html = $arrArticle['html'];
    	
		
		
		if($GLOBALS['TL_CONFIG']['webShop_facebook'])
			$this->generateFBMetaData($arrArticle);
    
    }

    
    
    protected function collectGalleryImages($arrI, $arrImages) {
    	foreach($arrI as $image)
    	  if(!in_array($image, $arrImages))
    	    $arrImages[] = $image;
    	    
    	return($arrImages);
    }
    
    protected function getArticleTabs($arrArticle) {
      /* check for tab-text */
      $res = $this->Database->prepare('SELECT * from tl_webshop_tabtext WHERE pid=? ORDER BY sorting')->execute($arrArticle['id']);
      if($res->numRows < 1)
        return(false);
      else {
        $GLOBALS['TL_JAVASCRIPT']['ws_tabs'] = 'system/modules/webShop/html/ws_tabs.js';
        $arrTabs = array();
        $arrContents = array();
        
        while($res->next()) {
          $arrHeadline = deserialize($res->headline, true);
          $arrTabs[] = sprintf('<span class="ws_tabBtn" id="ctrlTab_%s"><%s>%s</%s></span>', $res->id, $arrHeadline['unit'], $arrHeadline['value'], $arrHeadline['unit']);
          $arrContents[] = sprintf('<div class="ws_tabContent" id="ctrlTC_%s">%s</div>', $res->id, $res->text);
        }
        
        return(sprintf('<div id="tabBox_%s"><div class="tabControls">%s</div><div class="tabContents">%s</div></div><script type="text/javascript"> new ws_tabs("tabBox_%s"); </script>', $arrArticle['id'], implode("\n", $arrTabs), implode("\n", $arrContents), $arrArticle['id']));
      }
    }
    
    protected function compileBlockpricesList($arrPrices, $taxid) {
      $taxes = $this->objTaxSystem->taxes;
      $this->loadLanguageFile('tl_webshop_article');
      $arrHTML = array();
      $arrHTML[] = sprintf('<thead><tr><td class="blockQTY">%s</td><td class="blockPrice">%s</td></tr></thead><tbody>', $GLOBALS['TL_LANG']['tl_webshop_article']['opValue'], $GLOBALS['TL_LANG']['tl_webshop_article']['opLabel']);
      foreach($arrPrices as $priceBlock) {
        $qty = $priceBlock['value'];
        $price = $priceBlock['label'];
        if(!strlen(trim($price))) continue;
        if(!strlen(trim($qty))) continue;
        if($GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
	        if(!$this->objTaxSystem->showBrutto) {
	          $price = $price / ($taxes[$taxid]['tax_rate'] / 100 + 1);
	        }
        } else {
	        if($this->objTaxSystem->showBrutto) {
	          $price = $price * ($taxes[$taxid]['tax_rate'] / 100 + 1);
	        }
        }
        $arrHTML[] = sprintf('<tr><td class="blockQTY">%s</td><td class="blockPrice">%s</td></tr>', $qty, formatPrice($price,  true));
      }
      
      if(count($arrHTML) > 1)
        return('<table border="0" cellspacing="0" cellpadding="0">'. implode("\n", $arrHTML) .'</tbody></table>');
      
      return('');
    }
    
		
		protected function compileArticle($id) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_article where published=? AND alias=? AND (start = "" OR start <= ?) AND (stop = "" OR stop >= ?)')->execute(1, $id, time(), time());
			if($res->numRows == 0) return(false);
			$arrArticle = $res->fetchAssoc();

			$this->formKey = $arrArticle['id'];
			return($this->compileSingle($arrArticle));
		}

		
	protected function compileSingle($arrArticle, $hasVariant = false) {
	    $objArticle = new webShopArticle();
	    $objArticle->imageConfig = array('width' => $this->thumbWidth, 'height' => $this->thumbHeight);
	    $objArticle->dataArray = $arrArticle;
	    $objArticle->qty = 1;
	        
	    $objArticle->taxes = $this->objTaxSystem->taxes;
	    
	    if(!strlen($arrArticle['addImage']) || !strlen($arrArticle['singleSRC'])) {
	      $arrArticle['singleSRC'] = $GLOBALS['TL_CONFIG']['webShop_fallBackImage'];
	      $arrArticle['addImage'] = 1;
	    }       
	    if(strlen($arrArticle['addImage']) && strlen($arrArticle['singleSRC'])) {
	      $objArticle->arrImage = array(
	        'thumb' => $this->getImage($arrArticle['singleSRC'], $this->thumbWidth, $this->thumbHeight),
	        'full' => $this->getImage($arrArticle['singleSRC'], $this->fullWidth, $this->fullHeight),
	        'orig' => $arrArticle['singleSRC']
	      );
	    }
	    $objArticle->isSample = $arrArticle['imageSample'];
	    $arrArticle = $objArticle->dataarray;
	    $arrArticle['price'] = $objArticle->price;
	    $arrArticle['singlePrice'] = $objArticle->singlePrice;
	      
	    $arrArticle['productgroups'] = $objArticle->productgroup;
	    
	    return($arrArticle);
	}
	

		
		protected function createPriceBlock($arrArticle) {
			$objT = new FrontendTemplate('webShop_priceLabel');
      $objT->isSpecialPrice = $arrArticle['isSpecialPrice'];
      $objT->price = $arrArticle['price'];
      $objT->singlePrice = $arrArticle['singlePrice'];
      
      /* tax label and shipping notice */
      $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      /* brutto? add Taxes and change label */
      if($this->objTaxSystem->showBrutto) {
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      }
      $objT->taxLabel = $taxLabel;
      if($arrArticle['type'] != 'download')
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
		
		protected function generateFBMetaData($arrArticle) {
			global $objPage;
			$arrMeta = array(
				'og:type' => 'article',
				'og:title' => $arrArticle['title'],
				'og:url' => $this->Environment->base . $this->Environment->request,
				'og:site_name' => $arrArticle['title'],
				'og:description' => $arrArticle['seoDescription'],
			 	'og:image' => $this->Environment->base . $this->getImage($arrArticle['singleSRC'], 130, 110, 'box')
			);
			
			$strMeta = '';
			foreach($arrMeta as $key => $val)
			  $strMeta .= sprintf('<meta property="%s" content="%s" />' ."\n", $key, $val);
			  
			$GLOBALS['TL_HEAD'][] = $strMeta;
		}
    
  }

?>
<?php 

  class webShopInsertTags extends Frontend {
  
    protected $objTax;
    
    public function replaceWebShopTags($strTag) {
      $arrTag = trimsplit('::', $strTag);
      if(strtolower($arrTag[0]) != 'webshop') return false;
      
      switch(strtolower($arrTag[1])) {
      case 'categoryroot': {
        $strCategory = $this->Input->get($GLOBALS['webShop']['categoryKeyword']);
        if(!strlen($strCategory)) return false;
        
        $objCategory = new webShopCategoryController();
        $objCategory->generate();
        $trail = $objCategory->trail();
        $arrRoot = $objCategory->getCategory($trail[0]);
        return(is_array($arrRoot) ? $arrRoot['title'] : $arrTag[2]);
        
      } break;
      case 'parentname': {
        $strCategory = $this->Input->get($GLOBALS['webShop']['categoryKeyword']);
        if(!strlen($strCategory)) return false;
        
        $objCategory = new webShopCategoryController();
        $objCategory->generate();
        $arrTrail = $objCategory->parentcategory;
        
        return(is_array($arrTrail) ? $arrTrail['title'] : $arrTag[2]);
      } break;
      case 'article': {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where id=? AND published=?')->execute($arrTag[2], 1);
        if($res->numRows < 1) return false;
        $this->objTax = new webShopTaxController();
        $objArticle = new webShopArticle();
        $objArticle->imageConfig = array('width' => $arrTag[3], 'height' => $arrTag[4]);
        $objArticle->dataArray = $res->fetchAssoc();
        
        $objArticle->taxes = $this->objTax->taxes;

        $objTemplate = new FrontendTemplate('webShop_articlelistitem_default');

	      $objTemplate->title = $objArticle->title;
	      $objTemplate->prices = $this->createPriceBlock($objArticle->price, $objArticle->singlePrice, $objArticle->isSpecialPrice, $objArticle->dataarray);
	      
	      /* tax label and shipping notice */
	      $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
	      
	      /* brutto? add Taxes and change label */
	      if($this->objTax->showBrutto) {
	       
	        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
	      }
	      $objTemplate->taxLabel = $taxLabel;
	      
	      $objTemplate->href = $objArticle->href;
	      $objTemplate->thumbnail = $objArticle->thumb;
	      
	      if(strlen($arrItem['isnew'])) {
	        $objTemplate->markAsNew = $GLOBALS['TL_CONFIG']['webShop_markAsNew'];
	      }
	      if(strlen($arrItem['specialoffer'])) {
	        $objTemplate->markAsOffer = $GLOBALS['TL_CONFIG']['webShop_markAsOffer'];
	      }
	      
	      if($arrItem['teaser'])
	        $objTemplate->teaser = $objArticle->teaser;
	       
	      $objTemplate->cssClass = $objArticle->type;
	      
	      return($objTemplate->parse());
        
      } break;
      default: return false;
      }
    }
    
  protected function createPriceBlock($price, $singlePrice, $isSpecialPrice, $arrArticle) {
    
      $objT = new FrontendTemplate('webShop_priceLabel');
      $objT->isSpecialPrice = $isSpecialPrice;
      $objT->price = $price;
      $objT->singlePrice = $singlePrice;

      /* tax label and shipping notice */
      $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['exTax'];
      
      /* brutto? add Taxes and change label */
      if($this->objTax->showBrutto) {
        $taxLabel = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['incTax'];
      }
      $objT->taxLabel = $taxLabel;
      $pageShipping = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToShipping'])->fetchAssoc();
      $objT->shippingNotice = sprintf($GLOBALS['TL_LANG']['webShop']['FE_LABEL']['shippingNoticeEx'], $this->generateFrontendUrl($pageShipping));
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
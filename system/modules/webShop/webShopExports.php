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
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    LGPL 
 * @filesource
 */


/**
 * Class webShopExports
 *
 * @copyright  Stefan Gandlau 2010-2012
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  abstract class webShopExports extends Controller {

    /* article storage */
    protected $arrArticle = array();
    
    /* page targets */
    protected $pageDetails = array();
     
    protected $Tax = Null;
    
    protected $arrAttributes = array();
    
    /* general export configuration */
    protected $config = array(
      'exportProtected' => false,
      'exportHidden' => false,
      'exportOutOfStock' => false,
      'exportVariants' => true
    );
    
    /* constructor */
    public function __construct() {
      parent::__construct();
      $this->Import('Database');
      if(is_numeric($GLOBALS['TL_CONFIG']['webShop_jumpToArticle']))
        $this->pageDetails = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToArticle'])->fetchAssoc();
      else
        die($GLOBALS['TL_LANG']['webShop']['errors']['config_detailpage']);
      
      
      $this->Import('Environment');
      $this->Tax = new webShopTaxController();
      
    }
    
    abstract public function compile();
    
    /* read all articles */
    public function loadArticleDB($arrCategories = array()) {
      if(count($arrCategories) < 1)
        $stmtArticles = $this->Database->prepare('SELECT t1.*, t2.alias calias from tl_webshop_article t1, tl_webshop_categories t2 where t1.pid = t2.id AND t1.published=? AND t2.published=?');
      else
        $stmtArticles = $this->Database->prepare('SELECT t1.*, tl_alias calias from tl_webshop_article t1, tl_webshop_categories t2 where t1.pid = t2.id AND t1.published=? AND t2.published=? t1.pid IN ('. implode(',', $arrCategories) .')');
      
      $objArticles = $stmtArticles->execute(1, 1);

      
	while($objArticles->next()) {
    	/* check protected articles */
		if($objArticles->protected) continue;
		if($objArticles->type == 'article' || $objArticles->type == 'download') {
	   		if(!$this->checkCategory($objArticles->pid)) continue;
	   		$this->arrArticle[$objArticles->id] = $objArticles->row();
    	}
	}
      
      
      return(true);

    }
    
    
    
    protected function checkCategory($id) {
      $res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=? AND published=? AND (start="" || start < ?) AND (stop = "" || stop > ?)')->execute($id, 1, time(), time());
      if($res->numRows < 1) return(false);
      $arrCategory = $res->fetchAssoc();
      
      if($arrCategory['protected']) return(false);
      if($arrCategory['pid'] > 0)
        return($this->checkCategory($arrCategory['pid']));
        
      return(true);
    }
    
    /* create full article link */
    public function generateArticleLink($alias, $calias, $strAdd) {
      if(strlen($strAdd))
        $alias .= '/'. $strAdd;
      return($this->Environment->base . $this->generateFrontendUrl($this->pageDetails, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $calias .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $alias));
    }
    
    /* return the full link to the article image */
    public function generateArticleImage($singleSRC) {
    	if(file_exists(TL_ROOT .'/'. $singleSRC))
		    return($this->Environment->base . $this->getImage($singleSRC, 640, false));
		else
		    return($this->Environment->base . $this->getImage($GLOBALS['TL_CONFIG']['webShop_fallBackImage'], 640, false));
    }
    
    public function getArticleSinglePrice($article, $webShop_currencyDecimal = '.', $webShop_currencyThausands = '') {
      
      $singlePrice = $article['singlePrice'];
      if($article['specialprice'] > 0 && ($article['specialprice_start'] == '' || $article['specialprice_start'] <= time()) && ($article['specialprice_stop'] == '' || $article['specialprice_stop'] > time()))
        $singlePrice = $article['specialprice'];
      
      $arrTax = $this->Tax->taxes[$article['taxid']];
      
      if($GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
        $brutto = $singlePrice;
        $netto = $brutto / (($arrTax['tax_rate'] / 100) + 1);
      } else {
        $netto = $singlePrice;
        $brutto = $netto * (($arrTax['tax_rate'] / 100) + 1);
      }
      return(number_format($brutto, $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $webShop_currencyDecimal, $webShop_currencyThausands));
      
    }
    
    /* return a single article */
    public function getArticleDetails($id) {
      if(is_array($this->arrArticle[$id]))
        return($this->arrArticle[$id]);
        
      return false;
    }
    
    /* return configuration options */
    public function __get($key) {
      if(array_key_exists($key, $this->config))
        return($key);
      
      return $this->$key;
    }
    
    /* set configuration options */
    public function __set($key, $value) {
      if(!strlen($key)) return;
      $this->config[$key] = $value;
    }
  
  }

?>
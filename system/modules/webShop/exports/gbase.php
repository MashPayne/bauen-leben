<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao webCMS
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
 * Class gbase
 *
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  class gbase extends webShopExports {
    
    /* export name */
    public $exportName = 'Google Base (Google Merchant Center)';
    
    /* save filename */
    public $saveFile = 'google';
    
    /* filename extension */
    public $saveFileExtension = 'xml';
    
    /* items */
    protected $arrItems = array();
    
    public function __construct() {
      parent::__construct();
    }

    public function compile() {
      if(!$this->loadArticleDB()) return;
      
      /* xml */
	  $objT = new FrontendTemplate('export_gbase');
	  $objT->url = $this->Environment->base;
	  
	  
	  foreach($this->arrArticle as $index => $article) {
	  	$t = new FrontendTemplate('export_gbase_item');
	  	$t->title = $this->cleanUp($article['title']);
	  	$t->price = $this->getArticleSinglePrice($article, ',');
	  	$t->image = $this->generateArticleImage($article['singleSRC']);
	  	$t->description = $this->cleanUp(($article['seoDescription'] ? $article['seoDescription'] : $article['description']));
	  	$t->href = $this->generateArticleLink($article['alias'], $article['calias'], $article['addToUrl']);
	  	$t->id = $article['id'];
	  	$arrItems[] = $t->parse();
	  }
	  $objT->items = $arrItems;
	  @unlink(TL_ROOT .'/tl_files/'. $this->saveFile .'.'. $this->saveFileExtension);
	  $objFile = new File('tl_files/'. $this->saveFile .'.'. $this->saveFileExtension);
	  $objFile->write($objT->parse());
	  $objFile->close();
	  
    }
    
    public function cleanUp($strText) {

      $strText = str_replace("\n", " ", $strText);
      $strText = str_replace("\r", " ", $strText);
      $strText = str_replace("\t", " ", $strText);
      $strText = str_replace(chr(9), ' ', $strText);
	  $strText = str_replace('&', '&amp', $strText);
	  $strText = str_replace("'", '', $strText);
      return((strip_tags($strText)));
      
    }
  
  }

?>
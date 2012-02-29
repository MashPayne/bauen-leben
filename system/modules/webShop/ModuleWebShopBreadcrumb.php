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
 * Class ModuleWebShopBreadcrumb
 *
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleWebShopBreadcrumb extends Module {
    
    protected $strTemplate = 'webShop_navi_breadcrumb';
    protected $jumpDetails = array();
    protected $jumpCategory = array();
    
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### Breadcrumb (webShop) ###';
        return($t->parse());
      }
      return(parent::generate());
    }
    
    protected function compile() {
      $article = $this->Input->get($GLOBALS['webShop']['articleKeyword']);
      $category = $this->Input->get($GLOBALS['webShop']['categoryKeyword']);
      $group = $this->Input->get($GLOBALS['webShop']['groupKeyword']);
      if(!strlen($category) && !strlen($article)) {
        $this->Template->items = array();
        return;
      }
      
      $this->jumpCategory = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCategory'])->fetchAssoc();
      $this->jumpDetails = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToArticle'])->fetchAssoc();
      
      $arrPages = array_reverse($this->CBreadcrumb($category));
      
      if(strlen($article)) {
        $res = $this->Database->prepare('SELECT * from tl_webshop_article where alias=?')->execute($article);
        if($res->numRows > 0) {
          $arrArticle = $res->fetchAssoc();
          $arrPages[] = array(
            'title' => $arrArticle['title'],
            'href' => $this->generateFrontendUrl($this->jumpDetails, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $category .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $article),
            'link' => $arrArticle['title'],
            'isActive' => true
          );
        }
      }
      
      $this->Template->items = $arrPages;
    }
    
    protected function CBreadcrumb($strCat) {
      $arrPages = array();
      $res = $this->Database->prepare('SELECT * from tl_webshop_categories where alias=?')->execute($strCat);
      if($res->numRows < 1)
        return(array());
        
      $arrCat = $res->fetchAssoc();
      $arrPages[] = array(
        'title' => $arrCat['title'],
        'href' => $this->generateCategoryLink($arrCat['alias']),
        'link' => $arrCat['title'],
        'isActive' => strlen($this->Input->get($GLOBALS['webShop']['articleKeyword'])) ? false : true
      );
      
      while($arrCat['pid'] > 0) {
        $res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($arrCat['pid']);
        if($res->numRows > 0) {
          $arrCat = $res->fetchAssoc();
          if($arrCat['type'] == 'forward')
            $arrCat = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($arrCat['linkTarget'])->fetchAssoc();
          if($arrCat['type'] == 'page') {
            $arrTmp = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($arrCat['tl_page'])->fetchAssoc();
            $arrPages[] = array(
              'title' => $arrCat['title'],
              'href' => $this->generateFrontendUrl($arrTmp),
              'link' => $arrCat['title']
            );
          } else {
	          $arrPages[] = array(
	            'title' => $arrCat['title'],
	            'href' => $this->generateCategoryLink($arrCat['alias']),
	            'link' => $arrCat['title']
	          );
          }
        } else
          break;
      }
      
      return($arrPages);
    }
    
    protected function generateCategoryLink($alias) {
      return($this->generateFrontendUrl($this->jumpCategory, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $alias));
    }
    
    protected function generateArticleLink($category, $alias) {
      return($this->generateFrontendUrl($this->jumpDetails, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $category .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $article));
    }
    
  }

?>
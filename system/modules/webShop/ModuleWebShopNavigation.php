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
  * Class ModuleWebShopNavigation
  *
  * @copyright  Stefan Gandlau 2009
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  */

  class ModuleWebShopNavigation extends Module {
    
    protected $strTemplate = 'webShop_navigation_default';
    private $objCategory;
    private $arrJump;
    private $rootline = array();
    protected $trail = array();
    
    public function generate()
    {
      if(TL_MODE == 'BE') {
        $objTemplate = new BackendTemplate('be_wildcard');
        $objTemplate->wildcard = '### WEBSHOP NAVIGATION MODULE ###';
        return $objTemplate->parse();
      }
      if(FE_USER_LOGGED_IN) {
        $this->Import('FrontendUser', 'User');
      }
      $this->objCategory = new webShopCategoryController($this);
      $this->objCategory->generate();
      $this->trail = $this->objCategory->trail();
      
      $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
      $this->arrJump = $res->fetchAssoc();
      
      return parent::generate();
    }
    
    protected function compile() {
      global $objPage;
      $GLOBALS['TL_LANGUAGE'] = $objPage->language;
      $this->Template->navigation = $this->renderShopNavigation($this->webShop_startPoint);
      $this->Template->request = $this->Environment->request;
	  $this->Template->skipId = 'skipNavigation'.$this->id;
    }
  
    protected function renderShopNavigation($pid, $level=1) {
      global $objPage;
      $objSubcategories = $this->Database->prepare("SELECT * FROM tl_webshop_categories WHERE pid=? AND (start=? OR start<?) AND (stop=? OR stop>?) AND hide=? AND published=? ORDER BY sorting")->execute($pid, '', time(), '', time(), '', 1);
  
      if ($objSubcategories->numRows < 1) {
        return '';
      }

      $count = 0;
      $limit = $objSubcategories->numRows;
      $items = array();
      $groups = array();
  
      // Get all groups of the current front end user
      if (FE_USER_LOGGED_IN) {
        $groups = $this->User->groups;
      }
      
      $objTemplate = new FrontendTemplate($this->webShop_navigationTemplate);
  
      $objTemplate->type = get_class($this);
      $objTemplate->level = 'level_' . $level++;
  
      while($objSubcategories->next()) {
        $class = '';
        if(++$count == 1) {
          $class .= ' first';
        }
  
        if($count == $limit) {
          $class .= ' last';
        }
  
        $subitems = '';
        $_groups = deserialize($objSubcategories->groups);
  
        // Do not show protected pages unless a back end or front end user is logged in
        if (!strlen($objSubcategories->protected) || (!is_array($_groups) && FE_USER_LOGGED_IN) || BE_USER_LOGGED_IN || (is_array($groups) && $this->objCategory->isAllowed($groups))) {
          // Check whether there will be subpages
          if (!$this->showLevel || $this->showLevel >= $level || (!$this->hardLimit && ($this->objCategory->id == $objSubcategories->id || in_array($this->objCategory->id, $this->getChildRecords($objSubcategories->id, 'tl_webshop_categories'))))) {
            $subitems = $this->renderShopNavigation($objSubcategories->id, $level);
          }
  
          // Get href
          switch ($objSubcategories->type) {
            case 'forward': {
              $objNext = $this->Database->prepare("SELECT id, alias FROM tl_webshop_categories WHERE id=?")
                            ->limit(1)
                            ->execute($objSubcategories->linkTarget);
  
              if ($objNext->numRows)
              {
                $href = $this->generateFrontendUrl($this->arrJump, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $objNext->alias);
                break;
              }
            }  // DO NOT ADD A break; STATEMENT
            case 'page': {
              $objNext = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->limit(1)->execute($objSubcategories->tl_page);
              if($objNext->numRows) {
                $href = $this->generateFrontendUrl(array('id' => $objNext->id, 'alias' => $objNext->alias));
                break;
              }
            }
            default:
              $href = $this->generateFrontendUrl($this->arrJump, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $objSubcategories->alias);
              break;
          }
  
          // Active page
          if (($this->objCategory->id == $objSubcategories->id || $objSubcategories->type == 'forward' && $this->objCategory->id == $objSubcategories->linkTarget)) {
            $strClass = trim((strlen($subitems) > 0 ? 'submenu' : ''));
  
            $items[] = array(
              'isActive' => true,
              'subitems' => $subitems,
              'class' => trim((strlen($strClass) ? $strClass : '') . $class),
              'pageTitle' => specialchars($objSubcategories->title),
              'title' => specialchars($objSubcategories->title),
              'link' => $objSubcategories->title,
              'href' => $href,
              'categoryImage' => $objSubcategories->categoryImage
           );
       
            continue;
          }

          $strClass = trim((strlen($subitems) > 0 ? 'submenu' : ''));
          $strClass .= in_array($objSubcategories->id, $this->trail) ? ' trail' : '';
          
          $items[] = array(
            'isActive' => false,
            'subitems' => $subitems,
            'class' => trim((strlen($strClass) ? $strClass : '') . $class),
            'pageTitle' => specialchars($objSubcategories->title),
            'title' => specialchars($objSubcategories->title),
            'link' => $objSubcategories->title,
            'href' => $href,
          	'categoryImage' => $objSubcategories->categoryImage
          );
        }
      }
  
      $objTemplate->items = $items;
      return count($items) ? $objTemplate->parse() : '';
    }
  
  }

?>
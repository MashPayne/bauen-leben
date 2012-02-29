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
 * Class ModuleWSCloud
 *
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleWSCloud extends Module {
    
    protected $strTemplate = 'webshop_cloud';
	protected $arrGroups = array();
	protected $arrJump = array();
	protected $arrTags = array();
	
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### TagCloud (webShop) ###';
        return($t->parse());
      }
      
      if(FE_USER_LOGGED_IN) {
        $this->Import('FrontendUser', 'User');
        $this->arrGroups = $this->User->groups;
      }
      
      /* tax controll */
      $this->Import('webShopTaxController', 'objTaxSystem');
      
      global $objPage;
      $this->arrJump = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo)->fetchAssoc();
      
      return(parent::generate());
    }
    
	protected function compile() {
		/* get all categories */
		$arrCategories = $this->getCategoryList(0);
		
		/* get all tags from articles */
		$total = 0;
		$res = $this->Database->prepare('SELECT * from tl_webshop_article where pid IN ('. implode(',', $arrCategories) .')')->execute();
		while($res->next()) {
			if(!strlen(trim($res->tags))) continue;
			$arrTags = explode(',', $res->tags);
			foreach($arrTags as $tag) {
			  $tag = trim($tag);
			  if(!is_array($this->arrTags[$tag]))
			    $this->arrTags[$tag] = array();
			    
			  $this->arrTags[$tag][] = $res->id;
			  $total++;
			}
		}
		
		foreach($this->arrTags as $index => $items) {
		  $this->arrTags[$index] = array('items' => $items, 'count' => count($items));
		}
		
		$this->arrTags = $this->sortByField($this->arrTags, 'count', true);
		
		$fontMax = $this->webShop_cloudSizeMax;
		$fontMin = $this->webShop_cloudSizeMin;
		$diff = $fontMax - $fontMin;
		$percMax = -1;
		
		
		$arrTags = array();
		/* calculate font-size */
		foreach($this->arrTags as $index => $tag) {
		  $count = $tag['count'];
		  $tag = $tag['items'];
		  
		  if($percMax == -1) $percMax = $count;
		  
		  $fsize = $fontMin + ($diff * ($count / $percMax));
		  $arrTags[] = sprintf('<a style="font-size: %spx;" href="%s">%s</a>', $fsize, $this->generateFrontendUrl($this->arrJump, '/tag/'. $index), $index);
		}
		
		if($this->webShop_cloudLimit > 0)
			$arrTags = array_slice($arrTags, 0, $this->webShop_cloudLimit);
			
		$this->Template->arrTags = $arrTags;
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
    
    protected function getCategoryList($id) {
    	$arrIds = array($id);
    	$res = $this->Database->prepare('SELECT * from tl_webshop_categories WHERE pid=? AND published=?')->execute($id, 1);
    	if($res->numRows < 1) return($arrIds);
    	while($res->next()) {
    	  if($res->start != '' && (int) $res->start > time()) continue;
    	  if($res->stop != '' && (int) $res->stop < time()) continue;
    	  if($res->protected) {
    	    if(!FE_USER_LOGGED_IN) continue;
    	    $groups = deserialize($res->groups, true);
    	    if(!array_intersect($groups, $this->arrGroups)) continue;
    	  }
    	  $tmp = $this->getCategoryList($res->id);
    	  $arrIds = array_merge($arrIds, $tmp);
    	  
    	}
    	
    	return($arrIds);
    }
    
  }

?>
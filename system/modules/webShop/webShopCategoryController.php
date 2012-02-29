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
  * Class webShopCategoryController
  *
  * @copyright  Stefan Gandlau 2009
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  */
	
  class webShopCategoryController extends Controller {
  	
		protected $configuration = array();
		protected $categoryid = '';
		
		public function __construct() {
			$this->Import('Database');
      $this->Import('Input');
			if(FE_USER_LOGGED_IN)
			  $this->Import('FrontendUser', 'User');
				
		  parent::__construct();
		}
		
		public function trail() {
		  $arrPids = array();
		  $id = $this->configuration['id'];
		  do {
		    $res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($id);
		    $arrCat = $res->fetchAssoc();
		    $arrPids[] = $arrCat['id'];
		    $id = $arrCat['pid'];
		  } while($id > 0);
		  return(array_reverse($arrPids));
		}
		
    public function generate($cid = false) {
			
			if(!$cid)
        $this->categoryid = $this->Input->get($GLOBALS['webShop']['categoryKeyword']);
			else
			  $this->categoryid = $cid;
				
			if(strlen($this->categoryid)) {
				$res = $this->Database->prepare('SELECT * from tl_webshop_categories where published=? AND alias=?')->execute('1', $this->categoryid);
				if($res->numRows == 0) return;
				$this->configuration = $res->fetchAssoc();
			}
    }
		
		public function isAllowed($arrGroups) {
			$cGroups = deserialize($this->configuration['groups']);
			if(is_array($cGroups) && is_array($arrGroups))
  			if(array_intersect($arrGroups, $cGroups))
	 		    return true;
				
		  return false;
		}
		
		public function getSubCategories() {
			$arrRes = array();
			$res = $this->Database->prepare('SELECT * from tl_webshop_categories where published=? AND pid=? ORDER BY sorting')->execute('1', $this->configuration['id']);
			if($res->numRows == 0) return(false);
			while($res->next()) {
				if($res->type == 'forward') {
					$tmp = $this->getCategory($res->linkTarget);
					$tmp['title'] = $res->title;
					$arrRes[] = $tmp;
				} else {
					$arrRes[] = $res->row();
				}
			}
			return($arrRes);
		}
		
		protected function getParentCategory($id = false) {
			if($this->configuration['pid'] == 0 && $id == false) return(false);
			$res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($id == false ? $this->configuration['pid'] : $id);
			if($res->numRows == 0) return(false);
			return($res->fetchAssoc());
		}
		
		public function getCategory($id) {
		  $res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($id);
		  if($res->numRows) return($res->fetchAssoc());
		  return false;
		}
		
		public function __get($strKey) {
			switch(strtolower($strKey)) {
				case 'parentcategory': return($this->getParentCategory()); break;
				default: {
		      if(array_key_exists($strKey, $this->configuration))
		        return($this->configuration[$strKey]);
		        
		      return '';					
				}
			}

		}

    public function __set($strKey, $strVal) {
    	$this->configuration[$strKey] = $strVal;
    }
		
  }

?>
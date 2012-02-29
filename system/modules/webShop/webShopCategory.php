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
 * Class webShopCategory
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopCategory extends Controller {
    
    // storage
    protected $arrItemConfig = array();
    protected $arrJump = array();
    
    // options

    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
      if(FE_USER_LOGGED_IN) {
        $this->Import('FrontendUser', 'User');
      }
      
      parent::__construct();
    }
		
		public function load($id) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_categories where id=?')->execute($id);
			if($res->numRows == 0) return;
			$this->arrItemConfig = $res->fetchAssoc();
			$res = $this->Database->prepare('SELECT * from tl_webshop_categories where pid=?')->execute($this->arrItemConfig['id']);
			if($res->numRows == 0) return;
			$this->arrItemConfig['subcategories'] = $res->fetchAllAssoc();
		}
		    
    public function __get($key) {
      switch(strtolower($key)) {
      	case 'hassubcategory': return(count($this->arrItemConfig['subcategories']) ? true : false); break;
        default: {
          if(array_key_exists($key, $this->arrItemConfig)) {
            return($this->arrItemConfig[$key]);
          } else {
            return('unknown key: '. $key);
          }
        }
      }
    }
    
    public function __set($key, $value) {
      switch(strtolower($key)) {
        default: {
          $this->arrItemConfig[$key] = $value;
        }
      }
    }
    
  }

?>
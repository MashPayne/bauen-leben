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
 * Class ModuleWebShopMyOrders
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

  require_once('functions.php');

  class ModuleWebShopMyOrders extends Module {
    
    protected $strTemplate = 'webShop_myOrders';
    protected $arrLocal = array();
    protected $oid;
    protected $orderdata = '';
    
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### ORDERS HISTORY ###';
        return($t->parse());
      }
			
			if(FE_USER_LOGGED_IN)
			  $this->Import('FrontendUser', 'User');

			global $objPage;
			$this->arrLocal = array('id' => $objPage->id, 'alias' => $objPage->alias);
			
      return(parent::generate());
    }
    
    protected function compile() {
    	$this->loadLanguageFile('tl_webshop_orders');
    	if($this->Input->get('order') == '') {
      $arrOrders = $this->readUserOrders();
			$this->Template->items = $arrOrders;
    	} else {
    	  $this->Template = new FrontendTemplate('webShop_orderDetails');
    	  $this->orderDetails($this->Input->get('order'));
    	}
    }
		
		protected function readUserOrders() {
			$res = $this->Database->prepare('SELECT * from tl_webshop_orders where pid=?')->execute($this->User->id);
			if($res->numRows == 0) return(array());
			$arrAll = $res->fetchAllAssoc();
			foreach($arrAll as $index => $o)
			  $arrAll[$index]['details'] = $this->generateFrontendUrl($this->arrLocal, '/order/'. $o['id']);
			  
			return($arrAll);
		}
		
		protected function orderDetails($oid) {
		  $this->oid = $oid;
		  $arrArticle = array();
		  $res = $this->Database->prepare('SELECT t1.*, t2.payed from tl_webshop_orderitems t1, tl_webshop_orders t2 where t1.pid=t2.id AND t2.pid=? AND t2.id=?')->execute($this->User->id, $oid);
		  if($res->numRows > 0) {
		    while($res->next()) {
		      $tmp = $res->row();
		      $arrArticle[] = $tmp;
		    }
		  }
		  $this->Template->details = $arrArticle;
		}
		
		
		
		protected function getArticleData($id, $vid) {
		  $res = $this->Database->prepare('SELECT * From tl_webshop_article where id=?')->execute($id);
		  if($res->numRows > 0) {
		    $arrArticle = $res->fetchAssoc();
		    return($arrArticle);
		  }
		}
    
  }

?>
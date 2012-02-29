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
 * Class webShopAddressBook
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopAddressBook extends Controller {

    protected $arrError = array();
    protected $arrAddresses = array();
		protected $arrAddressFields = array(
		  'company', 'gender', 'title', 'firstname', 'lastname', 'street', 'postal', 'city', 'country'
		);
		protected $arrRequired = array('gender', 'firstname', 'lastname', 'street', 'postal', 'city', 'country', 'email');
		protected $specialAddressFields = array();
		
		protected $selected = 0;
		
		protected $guestAddressOK = true;
		
		public $errorBilling = array();
		public $errorShipping = array();
		
		
    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
			$this->Import('Config');
      if(FE_USER_LOGGED_IN)
        $this->Import('FrontendUser', 'User');
			
			// load required files
	    $this->loadLanguageFile('tl_member');
			$this->loadDataContainer('tl_member');
			
			$this->specialAddressFields = array(
        'gender' => $GLOBALS['TL_LANG']['MSC'],
				'country' => $this->getCountries() 
      );
			
			// initial load addresses
			$this->arrAddresses = $this->loadAddressbook();
      parent::__construct();
    }
		
		public function __destruct() {

		}
		
		public function getAddress($id) {
			if(array_key_exists($id, $this->arrAddresses))
			  return($this->arrAddresses[$id]);
			return(false);
		}
		
    protected function addAddress($arrAddress) {
    	$arrAddress['tstamp'] = time();
			$arrAddress['pid'] = $this->User->id;
    	$res = $this->Database->prepare('INSERT INTO tl_member_addressbook %s')->set($arrAddress)->execute();
			$newId = $res->insertId;
			if(strlen($this->Input->post('setPrimary'))) {
			  $res = $this->Database->prepare('UPDATE tl_member set defaultAddress=? WHERE id=?')->execute($newId, $this->User->id);
				$this->User->defaultAddress = $newId;
			}
			return($newId);
    }
		
		protected function updateAddress($arrAddress, $id) {
			$res = $this->Database->prepare('UPDATE tl_member_addressbook %s WHERE id=? AND pid=?')->set($arrAddress)->execute($id, $this->User->id);
			if(strlen($this->Input->post('setPrimary'))) {
			  $res = $this->Database->prepare('UPDATE tl_member set defaultAddress=? where id=?')->execute($id, $this->User->id);
				$this->User->defaultAddress = $id;
			}
			return($id);
		}
		
		protected function updateUserAddress($arrSet) {
			if(strlen($this->Input->post('setPrimary'))) {
			  $arrSet['defaultAddress'] = 0;
				$this->User->defaultAddress = $newId;
			}
				
			$this->Database->prepare('UPDATE tl_member %s where id=?')->set($arrSet)->execute($this->User->id);
			return(0);
		}
		
		public function saveAddress() {
			$arrSet = array();
      		foreach($this->arrAddressFields as $fld)
			  $arrSet[$fld] = $this->Input->post($fld);
			
			$aid = $this->Input->post('addressID');
			if($this->Input->post('deleteAddress') && $aid != 0) {
				$this->deleteAddress($aid);
			} else {
				if(strlen($aid) == 0) return($this->addAddress($arrSet));
				elseif($aid == 0) return($this->updateUserAddress($arrSet));
				elseif(is_numeric($aid) && $aid > 0) return($this->updateAddress($arrSet, $aid));
			}
			
		}
		
		protected function deleteAddress($id) {
			$res = $this->Database->prepare('DELETE from tl_member_addressbook where id=? AND pid=?')->execute($id, $this->User->id);
		}
		
    public function __get($key) {
    	switch(strtolower($key)) {
    		case 'addresses':
        case 'address': return($this->arrAddresses); break;
        case 'addressformated': return($this->formatAddresses($this->arrAddresses)); break;
				case 'primary': {
					if(FE_USER_LOGGED_IN && array_key_exists($this->User->defaultAddress, $this->arrAddresses))
					  return($this->arrAddresses[$this->User->defaultAddress]);
					return($this->arrAddresses[0]);
				} break;
				case 'selected': 
					if(FE_USER_LOGGED_IN) {
						return($this->selected); 
					} else {
						$arrCheck = array('firstname', 'lastname', 'street', 'postal', 'city', 'country');
	  				  	$check = true;
	  				  	foreach($arrCheck as $fld)
	  				    if(!strlen(trim($this->arrAddresses[1][$fld])))
	  				      $check = false;
	  				      
	  				  if($check)
	  				    return(1);
	  				  else
	  				    return(0);
	  				    
					} break;
					
				case 'shippingaddress': {
				  
				  if(FE_USER_LOGGED_IN)
  					return($this->arrAddresses[$this->selected]);
  				else {
  				  $arrCheck = array('firstname', 'lastname', 'street', 'postal', 'city', 'country');
  				  $check = true;
  				  foreach($arrCheck as $fld)
  				    if(!strlen(trim($this->arrAddresses[1][$fld])))
  				      $check = false;
  				      
  				  if($check)
  				    return($this->arrAddresses[1]);
  				  else
  				    return($this->arrAddresses[0]);
  				} 
				} break;
				case 'primaryid': return(is_array($this->arrAddresses[$this->User->defaultAddress]) ? $this->User->defaultAddress : 0); break;
				case 'fields': return($this->arrAddressFields); break;
				case 'addressok': return($this->guestAddressOK);
    	}
    	return($this->$key);
    }
		
		public function __set($key, $value) {
			switch(strtolower($key)) {
				case 'select':
				case 'selected': {
					$this->selected = $value; 
				} break;
			}
			$this->$key = $value;
		}
		
		protected function formatAddresses($arrA) {
			foreach($arrA as $id => $arrAddress) {
				foreach($arrAddress as $fld => $value) {
					if(in_array($fld, array_keys($this->specialAddressFields)))
					  $arrA[$id][$fld] = $this->specialAddressFields[$fld][$value];
				}
			}
			return($arrA);
		}
		
		public function reloadAddressBook() {
		  $this->arrAddresses = $this->loadAddressbook();
		}
		
		protected function loadAddressbook() {	
			$arrReturn = array();
			if(FE_USER_LOGGED_IN) {
				$arrReturn[] = $this->getUserAddress();
				$res = $this->Database->prepare('SELECT * from tl_member_addressbook where pid=?')->execute($this->User->id);
				if($res->numRows > 0) {
					while($res->next()) {
						$tmp = array();
						foreach($this->arrAddressFields as $fld)
	 					  $tmp[$fld] = $res->$fld;
						if($this->User->defaultAddress == $res->id)
						  $tmp['primary'] = true;
							
						$tmp['formated'] = $this->formatAddress($tmp);
						$tmp['id'] = $res->id;
						$tmp['uid'] = $this->User->id;
						$arrReturn[$res->id] = $tmp;
					}
				}
			} else {
				if($GLOBALS['TL_CONFIG']['webShop_guestOrder']) {
					$arrReturn[0] = $_SESSION['webShop']['guest']['billing'];
	        		$arrReturn[1] = $_SESSION['webShop']['guest']['shipping'];
			  	}
			}
			return($arrReturn);
		}
		
		protected function formatAddress($arrAddress) {
			return(sprintf('%s %s, %s, %s, %s, %s', $arrAddress['firstname'], $arrAddress['lastname'], $arrAddress['street'], $arrAddress['postal'], $arrAddress['city'], $arrAddress['country']));
		}
		
		protected function getUserAddress() {
			foreach($this->arrAddressFields as $fld)
  				$tmp[$fld] = $this->User->$fld;
			if($this->User->defaultAddress == 0)
				$tmp['primary'] = true;
			$tmp['formated'] = $this->formatAddress($tmp);
			$tmp['id'] = 0;
			$tmp['uid'] = $this->User->id;
      		return($tmp);
		}
		
		public function guestShipping() {
		  $strForm = '';
		  $strKey = 'guest_shipping_';
		  $isActive = false;
		  foreach($this->arrAddressFields as $fld)
		    if($this->Input->post($strKey .$fld) != '')
		      $isActive = true;
		      
		  if(!is_array($_SESSION['webShop']['guest']))
		    $_SESSION['webShop']['guest'] = array();
		    
		  $this->loadDataContainer('tl_member');
		  $this->loadLanguageFile('tl_member');
		  foreach($this->arrAddressFields as $fld) {
		    $config = $GLOBALS['TL_DCA']['tl_member']['fields'][$fld];
		    $config['eval']['mandatory'] = false;
		    if($isActive && in_array($fld, $this->arrRequired)) {
		    	$config['eval']['mandatory'] = true;
		    	$config['eval']['required'] = true;
		    	
		    }
		    $cls = $GLOBALS['TL_FFL'][$config['inputType']];
		    $objWdg = new $cls($this->prepareForWidget($config, $strKey . $fld));
		    $objWdg->label = $GLOBALS['TL_LANG']['tl_member'][$fld][0];
		    if($this->Input->post('FORM_ACTION') == 'webShopCheckout') {
		      $value = $this->Input->post($strKey . $fld);
		      $objWdg->value = $value;
		      $objWdg->validate();
		      if(!$objWdg->hasErrors())
		        $_SESSION['webShop']['guest']['shipping'][$fld] = $value;
		      else
		        $this->errorShipping[] = $objWdg->getErrorAsHTML();
		    }
		    
		    if($_SESSION['webShop']['guest']['shipping'][$fld] != '')
		      $objWdg->value = $_SESSION['webShop']['guest']['shipping'][$fld];
		    $strForm .= '<div class="formelem">';
		    $strForm .= $objWdg->generateLabel() . $objWdg->generate() .'</div>';
		  }
		  return($strForm);
		}
		
		public function guestBilling() {
      $strForm = '';
      $strKey = 'guest_billing_';
      
      $hasError = false;
      if(!is_array($_SESSION['webShop']['guest']))
        $_SESSION['webShop']['guest'] = array('billing' => array());
        
      $this->loadDataContainer('tl_member');
      $arrFields = $this->arrAddressFields;
      $arrFields[] = 'email';
      $GLOBALS['TL_DCA']['tl_member']['fields']['gender']['eval']['mandatory'] = true;
      $this->loadLanguageFile('tl_member');
      $objT = new FrontendTemplate('webShop_form_address');
      foreach($arrFields as $fld) {
      	
        $config = $GLOBALS['TL_DCA']['tl_member']['fields'][$fld];
        $config['eval']['required'] = in_array($fld, $this->arrRequired);
        $cls = $GLOBALS['TL_FFL'][$config['inputType']];
        $objWdg = new $cls($this->prepareForWidget($config, $strKey . $fld));
        
        $objWdg->label = $GLOBALS['TL_LANG']['tl_member'][$fld][0];
        if($this->Input->post('FORM_ACTION') == 'webShopCheckout') {
          $value = $this->Input->post($strKey . $fld);
          $objWdg->value = $value;
          $objWdg->validate();
          if(!$objWdg->hasErrors())
            $_SESSION['webShop']['guest']['billing'][$fld] = $value;
          else {
           $hasError = true;
           $this->errorBilling[] = $objWdg->getErrorAsHTML();
          }
          
        } else {
          $objWdg->value = $_SESSION['webShop']['guest']['billing'][$fld];
        }
        
        if(($config['eval']['mandatory'] && !strlen($_SESSION['webShop']['guest']['billing'][$fld])) || $hasError)
          $this->guestAddressOK = false;

        $strForm .= '<div class="formelem">';
        $strForm .= $objWdg->generateLabel() . $objWdg->generate() .'</div>';
        
      }

      return($strForm);
		}
  }

?>
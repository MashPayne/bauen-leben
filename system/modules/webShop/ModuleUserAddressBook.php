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
 * Class ModuleUserAddressBook
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleUserAddressBook extends Module {
    
    protected $strTemplate = 'mod_useraddressbook';
				
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### USER ADDRESS BOOK ###';
        return($t->parse());
      }
			if(FE_USER_LOGGED_IN)
			  $this->Import('FrontendUser', 'User');
			
			$this->Import('webShopAddressBook', 'Book');
			
      return(parent::generate());
    }
    
    protected function compile() {
      switch($this->Input->post('FORM_ACTION')) {
      	case 'newAddress': $this->frmEditAddress(); break;
				case 'editAddress': {
					if(strlen($this->Input->post('deleteAddress'))) {
					  $this->Book->saveAddress();
						$this->reload();
					}
					$this->frmEditAddress($this->Input->post('addressID')); break;
				}
      	default: $this->overview();
      }
    }
		
		protected function frmEditAddress($idAddress = false) {
			if(is_numeric($idAddress))
			  $arrAddress = $this->Book->addresses[$idAddress];

      $submit = true;			
			$objT = new FrontendTemplate('mod_useraddressbook_edit');
			$arrFields = $this->Book->fields;
			$arrElem = array();
			foreach($arrFields as $fld) {
        $arrData = $GLOBALS['TL_DCA']['tl_member']['fields'][$fld];
        $strClass = $GLOBALS['TL_FFL'][$arrData['inputType']];
				if(!$this->classFileExists($strClass)) continue;
        $objWdg = new $strClass($this->prepareForWidget($arrData, $fld));
				if($this->Input->post('FORM_SUBMIT') == 'saveAddress') {
				  $objWdg->value = $this->Input->post($fld);
					$objWdg->validate();
					$objWdg->tableless = true;
					if($objWdg->hasErrors())
					  $submit = false;
				} else
				  if(is_array($arrAddress)) $objWdg->value = $arrAddress[$fld];
				
				$arrElem[] = $objWdg->parse();
			}
      if($submit && $this->Input->post('FORM_SUBMIT') == 'saveAddress') {
      	$this->Book->saveAddress();
				$this->reload();
      }
			$objT->formFields = $arrElem;
			$objT->isPrimary = ($idAddress == false ? false : ($idAddress == $this->User->defaultAddress ? true : false));
			$objT->addressid = $idAddress;
			$objT->formAction = $this->Input->post('FORM_ACTION');
			$this->Template->data = $objT->parse();
		}
		
		protected function overview() {
			$objT = new FrontendTemplate('mod_useraddressbook_overview');
			$objT->addresses = $this->Book->addressformated;
			$objT->primaryid = $this->Book->primaryid;
			$this->Template->data = $objT->parse();
		}
    
  }

?>
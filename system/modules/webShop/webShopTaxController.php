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
 * Class webShopTaxController
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class webShopTaxController extends Controller {
    
		protected $taxes = array();
		public $zone = false;
		public $showBrutto;
		public $calcBrutto;
		
    public function __construct() {
      $this->Import('Database');
      $this->Import('Input');
			if(FE_USER_LOGGED_IN) {
			  $this->Import('FrontendUser', 'User');
        $country = $this->User->country;
			} else {
				$country = $GLOBALS['TL_CONFIG']['webShop_country'];
			}
			$this->zone = $this->findTaxZone($country);
			$this->taxSystem();
			$this->taxes = $this->findTaxes($this->zone);
			parent::__construct();
    }
		
		protected function taxSystem() {
      $groups = array($GLOBALS['TL_CONFIG']['webShop_businessGroup']);
      
      // Land des Benutzers
      if(!FE_USER_LOGGED_IN || !$this->User->username) {
        $userCountry = $GLOBALS['TL_CONFIG']['webShop_country'];
        $_groups = array();
        $UST = false;
      } else {
        $userCountry = $this->User->country;
        $_groups = $this->User->groups;
        $UST = strlen($this->User->ustid) ? true : false;
      }
			
      $localZone = $this->checkZoneMembership($this->zone, $userCountry);
      //Ist Kunde Gew.
      $gew = array_intersect($groups, $_groups);
      
      if(count($gew) > 0) {
        // ########## Gewerblich #############
        if($userCountry == $GLOBALS['TL_CONFIG']['webShop_country']) {
          // im eigenen Land
          // Anzeige netto - Zahlung brutto
          $this->showBrutto = false;
					$this->calcBrutto = true;
        } else {
          // nicht im eigenen Land
          if($localZone) {
            // innerhalb der EU, nicht im eigenen Land
            // Anzeige netto - Zahlung brutto
						$this->showBrutto = false;
            $this->calcBrutto = true;
            if($UST && strlen($GLOBALS['TL_CONFIG']['webShop_vat'])) {
            	// innerhalb der EU, nicht im eigenen Land mit UstID (Kunde + Shop)
							// anzeige netto - Zahlung netto
              $this->showBrutto = false;
              $this->calcBrutto = false;
            }
          } else {
            // ausserhalb der EU
            // anzeige netto - zahlung netto
						$this->showBrutto = false;
            $this->calcBrutto = false;
          }
        }
        
      } else {
        // ########## Privat ######
        if($localZone) {
          // innerhalb der EU, nicht im eigenen Land
          // Anzeige brutto - Zahlung brutto
					$this->showBrutto = true;
          $this->calcBrutto = true;
        } else {
          // ausserhalb der EU
          // anzeige netto - zahlung netto
					$this->showBrutto = false;
          $this->calcBrutto = false;
        }
      }
		}
		
		protected function checkZoneMembership($zoneid, $country) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_taxzones where id=?')->execute($zoneid);
			$arrZone = $res->fetchAssoc();
			$arrCountries = deserialize($arrZone['taxCountries']);
			if(in_array($country, $arrCountries))
			  return(true);
			return(false);
		}
    
		protected function findTaxZone($country) {
			$res = $this->Database->prepare('SELECT * from tl_webshop_taxzones where published=?')->execute(1);
			if($res->numRows == 0) die('NO TAX ZONES DEFINED');
			while($res->next()) {
				$arrCountries = deserialize($res->taxCountries);
				if(in_array($country, array_values($arrCountries)))
				  return($res->id);
			}
			return($this->findTaxZone($GLOBALS['TL_CONFIG']['webShop_country']));
			
		}
		
		protected function findTaxes($id) {
			$arrTaxes = array();
			$res = $this->Database->prepare('SELECT * from tl_webshop_taxes where tax_zone=?')->execute($id);
			while($res->next()) {
				$t = $res->row();
				$t['showBrutto'] = $this->showBrutto;
				$t['calcBrutto'] = $this->calcBrutto;
				$t['sum'] = 0;
				$arrTaxes[$t['tax_class']] = $t;
			}
			return($arrTaxes);
		}
		
		public function __get($key) {
			switch(strtolower($key)) {
				case 'tax':
        case 'taxes': return($this->taxes); break;
				default: die('unkonwn key: '. $key);
			}
		}
    
  }

?>
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
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_webshop_paymentmodules
 */
$GLOBALS['TL_DCA']['tl_webshop_paymentmodules'] = array
(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 0,
      'fields'                  => array('title'),
    ),
    'label' => array
    (
      'fields'                  => array('title', 'paymentModule'),
      'format'                  => '%s [%s]',
      'label_callback'          => array('tl_webshop_paymentmodules', 'formatLabel')
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();"'
      )
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => 'title;paymentModule,paymentConfig;paymentText,paymentMail;discount;groups,guestAllowed;paymentTax;published',
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
    ),
		'paymentModule' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['paymentModule'],
			'inputType'               => 'select',
			'options_callback'        => array('tl_webshop_paymentmodules', 'getInstalledModules'),
			'eval'                    => array('includeBlankOption' => true, 'submitOnChange' => true)
		),
		'paymentConfig' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['paymentConfig'],
			'input_field_callback'    => array('tl_webshop_paymentmodules', 'getModuleConfig')
		),
		'published' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['published'],
			'inputType'               => 'checkbox'
		),
		'discount' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['discount'],
			'inputType'               => 'text'
		),
		'paymentTax' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['paymentTax'],
			'inputType'               => 'select',
			'foreignKey'              => 'tl_webshop_taxclasses.title',
			'eval'                    => array('mandatory' => true)
		),
		'groups' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['groups'],
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member_group.name',
			'eval'                    => array('multiple' => true, 'mandatory' => true)
		),
		'paymentText' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['paymentText'],
      'inputType'               => 'textarea',
      'eval'                    => array('rte' => 'tinyMCE')
    ),
    'paymentMail' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['paymentMail'],
      'inputType'               => 'textarea',
      'eval'                    => array('rte' => 'tinyMCE')
    ),
    'guestAllowed' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['guestAllowed'],
      'inputType'               => 'checkbox'
    )
  )
);

class tl_webshop_paymentmodules extends Backend {
 
  public function formatLabel($arrRow) {
    return(sprintf('%s [%s]%s', $arrRow['title'], $arrRow['paymentModule'], $arrRow['discount'] != '' ? ' '. number_format($arrRow['discount'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimal'], $GLOBALS['TL_CONFIG']['webShop_currencyThausands']) .' '. $GLOBALS['TL_CONFIG']['webShop_currencySign'] : ''));
  }
  
	public function getInstalledModules() {
		$arrModules = array();
		$base = TL_ROOT .'/system/modules/webShop/paymentModules/';
		$arrContents = scandir($base);
		foreach($arrContents as $c) {
			if(substr($c, 0, 1) == '.') continue;
			if(!file_exists($base . $c .'/'. $c .'.php')) continue;
			require_once($base . $c .'/'. $c .'.php');
			$objMod = new $c(array());
			$arrModules[$c] = $objMod->moduleInfo();
			
		}
		return($arrModules);
	}
	
	public function getModuleConfig(DataContainer $objDc) {
    $res = $this->Database->prepare("SELECT * from tl_webshop_paymentmodules where id=?")->execute($objDc->id);
    $arrModule = $res->fetchAssoc();
    if($arrModule['paymentModule'] == '') {
      return(sprintf('<h3><label for="ctrl_%s">%s</label></h3><i>Sie haben kein Zahlunsmodul ausgewählt.</i><p class="tl_help">%s</p>', $objDc->field, $GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['moduleConfig'][0], $GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['moduleConfig'][1]));
    } else {
      $baseDir = TL_ROOT .'/system/modules/webShop/paymentModules/';
      $m = $arrModule['paymentModule'];
      include_once($baseDir .'/'. $m .'/'. $m .'.php');
      $objPayment = new $m(deserialize($arrModule['paymentConfig']));
      return(sprintf('<h3><label for="ctrl_%s">%s</label></h3>%s<p class="tl_help">%s</p>', $objDc->field, $GLOBALS['TL_LANG']['webShop_paymentModules']['moduleConfig'][0], $objPayment->generateBEForm($arrModule['paymentConfig']), $GLOBALS['TL_LANG']['tl_webshop_paymentmodules']['moduleConfig'][1]));
    }
	}
	
}
?>
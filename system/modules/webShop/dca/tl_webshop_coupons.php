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
 * Table tl_webshop_coupons
 */
$GLOBALS['TL_DCA']['tl_webshop_coupons'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'enableVersioning'            => true,
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 2,
			'flag'                    => 1,
      'fields'                  => array('type'),
			'panelLayout'             => 'sort,filter;search,limit'
    ),
    'label' => array
    (
      'fields'                  => array('code', 'amount'),
      'format'                  => '%s <b>[%s]</b>',
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
	  '__selector__'            => array('type', 'limitUse'),
    'couponUser'              => 'type;code,amount;userid;limitUse,validUntil,couponTax;published',
		'couponAll'               => 'type;code,amount;limitUse,validUntil;couponTax;published'
  ),

  // Subpalettes
  'subpalettes' => array(
    'limitUse'                => 'maxUse'
  ),

  // Fields
  'fields' => array
  (
    'type' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['type'],
			'inputType'             => 'select',
			'options'               => array('couponUser', 'couponAll'),
			'default'               => 'couponAll',
			'reference'             => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['types'],
			'eval'                  => array('submitOnChange' => true),
			'filter'                => true,
			'sorting'               => true,
			'flag'                  => 11
		),
		'code' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['code'],
      'inputType'             => 'text',
			'eval'                  => array('maxlength' => 32, 'unique' => true),
			'search'                => true,
			'sorting'               => true,
			'flag'                  => 1,
			'save_callback'         => array(array('tl_webshop_coupons', 'generateCoupon'))
		),
		'amount' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['amount'],
			'inputType'             => 'text',
			'eval'                  => array('mandatory' => true)
		),
		'userid' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['userid'],
			'inputType'             => 'select',
			'options_callback'      => array('tl_webshop_coupons', 'customerList'),
			'eval'                  => array('includeBlankOption' => true, 'mandatory' => true),
			'filter'                => true
		),
		'limitUse' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['limitUse'],
			'inputType'             => 'checkbox',
			'eval'                  => array('submitOnChange' => true),
			'filter'                => true
		),
		'maxUse' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['maxUse'],
			'inputType'             => 'text',
			'eval'                  => array('mandatory' => true, 'rgxp' => 'digit')
		),
		'validUntil' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['validUntil'],
			'inputType'             => 'text',
			'eval'                  => array('rgxp' => 'date', 'datepicker' => $this->getDatePickerString())
		),
		'published' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['published'],
			'inputType'             => 'checkbox',
			'filter'                => true
		),
		'couponTax' => array(
		  'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_coupons']['couponTax'],
			'inputType'             => 'select',
			'foreignKey'            => 'tl_webshop_taxclasses.title',
			'eval'                  => array('mandatory' => true)
		)
  )
);

class tl_webshop_coupons extends Backend {
	
	public function customerList() {
		$arrReturn = array();
		$res = $this->Database->prepare('SELECT * from tl_member ORDER by lastname,firstname')->execute();
		while($res->next()) {
			$arrReturn[$res->id] = sprintf('%s, %s%s', $res->lastname, $res->firstname, $res->company != '' ? ' '. $res->company : '');
		}
		return($arrReturn);
	}
	
	public function generateCoupon($varValue, DataContainer $dc) {
		$codeChars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		if(!strlen($varValue)) {
			do {
				$varValue .= substr($codeChars, rand(0, strlen($codeChars)) - 1, 1);
			} while(strlen($varValue) < 15);
		}
		return(strtolower($varValue));
	}
	
}

?>
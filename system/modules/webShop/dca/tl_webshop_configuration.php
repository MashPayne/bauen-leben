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
 * webShop configuration
 */
$GLOBALS['TL_DCA']['tl_webshop_configuration'] = array
(

  // Config
  'config' => array
  (
    'dataContainer'               => 'File',
    'closed'                      => true
  ),

  // Palettes
  'palettes' => array
  (

    'default'                     => 'webShop_country,webShop_businessGroup,webShop_vat,webShop_mov;webShop_defaultTax,webShop_noShipping;webShop_currencySign,webShop_currencyThausands,webShop_currencyDecimal,webShop_currencyDecimals;webShop_pricesBrutto,webShop_disableCoupons,webShop_guestOrder,webShop_ajaxCart,webShop_displayVariants,webShop_variantSelectionType;webShop_facebook,webShop_twitter;webShop_fallBackImage,webShop_markAsNew,webShop_markAsOffer;webShop_jumpToCategory,webShop_jumpToArticle,webShop_jumpToCart,webShop_jumpToShipping;webShop_agb'
  ),

  // Subpalettes
  'subpalettes' => array
  (

  ),

  // Fields
  'fields' => array
  (
    'webShop_country' => array(
		'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_country'],
		'inputType' => 'select',
		'options' => $this->getCountries(),
		'default' => $GLOBALS['TL_LANGUAGE']
	),
	'webShop_variantSelectionType' => array(
		'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_variantSelectionType'],
		'inputType' => 'select',
		'options' => array('selectbox', 'buttons'),
		'reference' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_variantSelectionTypes'],
		'default' => 'selectbox'
	),
	'webShop_currencySign' => array(
    	'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_currencySign'],
    	'inputType' => 'text'
    ),
    'webShop_currencyThausands' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_currencyThausands'],
      'inputType' => 'text'
    ),
    'webShop_currencyDecimal' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_currencyDecimal'],
      'inputType' => 'text'
    ),
    'webShop_currencyDecimals' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_currencyDecimals'],
      'inputType' => 'text',
			'eval' => array('rgxp' => 'digit')
    ),
		'webShop_pricesBrutto' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_pricesBrutto'],
			'inputType' => 'checkbox'
		),
		'webShop_noShipping' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_noShipping'],
			'inputType' => 'text',
			'eval' => array('mandatory' => true)
		),
		'webShop_businessGroup' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_businessGroup'],
			'inputType' => 'radio',
			'foreignKey' => 'tl_member_group.name'
		),
		'webShop_vat' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_vat'],
			'inputType' => 'text'
		),
		'webShop_fallBackImage' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_fallBackImage'],
			'inputType' => 'fileTree',
			'eval' => array('files' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'jpeg,jpg,png,gif')
		),
		'webShop_markAsNew' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_markAsNew'],
			'inputType' => 'fileTree',
			'eval' => array('files' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'jpeg,jpg,png,gif')
		),
		'webShop_markAsOffer' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_markAsOffer'],
      'inputType' => 'fileTree',
      'eval' => array('files' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' => 'jpeg,jpg,png,gif')
		),
		'webShop_jumpToArticle' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_jumpToArticle'],
      'inputType' => 'pageTree',
      'eval' => array('mandatory' => true, 'fieldType' => 'radio')
    ),
    'webShop_jumpToCategory' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_jumpToCategory'],
      'inputType' => 'pageTree',
      'eval' => array('mandatory' => true, 'fieldType' => 'radio')
    ),
    'webShop_jumpToCart' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_jumpToCart'],
      'inputType' => 'pageTree',
      'eval' => array('mandatory' => true, 'fieldType' => 'radio')
    ),
    'webShop_jumpToShipping' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_jumpToShipping'],
      'inputType' => 'pageTree',
      'eval' => array('mandatory' => true, 'fieldType' => 'radio')
    ),
		'webShop_agb' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_agb'],
			'inputType' => 'textarea',
			'eval' => array('rte' => 'tinyMCE', 'mandatory' => true)
		),
		'webShop_defaultTax' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_defaultTax'],
			'inputType' => 'select',
			'foreignKey' => 'tl_webshop_taxclasses.title'
		),
    'webShop_disableCoupons' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_disableCoupons'],
      'inputType' => 'checkbox'
    ),
    'webShop_guestOrder' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_guestOrder'],
      'inputType' => 'checkbox'
    ),
    'webShop_mov' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_mov'],
      'inputType' => 'text',
      'eval' => array('rgxp' => 'digit')
    ),
    'webShop_facebook' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_facebook'],
      'inputType' => 'checkbox'
    ),
    'webShop_twitter' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_twitter'],
      'inputType' => 'checkbox'
    ),
    'webShop_displayVariants' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_displayVariants'],
      'inputType' => 'checkbox'    
    ),
    'webShop_ajaxCart' => array(
    	'label' => &$GLOBALS['TL_LANG']['tl_webshop_configuration']['webShop_ajaxCart'],
    	'inputType' => 'checkbox'
    )
  )
);

?>
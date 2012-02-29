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
 * webShop email configuration
 */
$GLOBALS['TL_DCA']['tl_webshop_emailconfig'] = array
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

    'default'                     => 'webShop_email_mail,webShop_email_name,webShop_email_subject;webShop_email_attachment;webShop_email_sendTo,webShop_email_footer'
  ),

  // Subpalettes
  'subpalettes' => array
  (

  ),

  // Fields
  'fields' => array
  (
    'webShop_email_mail' => array(
      'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_mail'],
      'inputType' => 'text',
      'eval' => array('rgxp' => 'email', 'mandatory' => true)
    ),
    'webShop_email_name' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_name'],
			'inputType' => 'text',
			'eval' => array('mandatory' => true)
		),
		'webShop_email_subject' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_subject'],
      'inputType' => 'text',
      'eval' => array('mandatory' => true)
		),
		'webShop_email_attachment' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_attachment'],
			'inputType' => 'fileTree',
			'eval' => array('fieldType' => 'checkbox', 'files' => true, 'filesOnly' => true)
		),
		'webShop_email_sendTo' => array(
		  'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_sendTo'],
			'inputType' => 'checkbox',
			'foreignKey' => 'tl_user.name',
			'eval' => array('multiple' => true, 'mandatory' => true)
		),
		'webShop_email_footer' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_webshop_emailconfig']['webShop_email_footer'],
			'inputType' => 'textarea',
			'eval' => array('rte' => 'tinyMCE')
		)
    
  )
);

?>
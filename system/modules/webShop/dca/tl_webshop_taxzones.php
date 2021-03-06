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
 * Table tl_webshop_taxzones
 */
$GLOBALS['TL_DCA']['tl_webshop_taxzones'] = array
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
      'mode'                    => 2,
      'fields'                  => array('published', 'title'),
    ),
    'label' => array
    (
      'fields'                  => array('title'),
      'format'                  => '%s',
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_taxzones']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_taxzones']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_webshop_taxzones']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => 'title,taxCountries;published',
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxzones']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
    ),
    'taxCountries' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxzones']['taxCountries'],
			'inputType'               => 'checkbox',
			'options'                 => $this->getCountries(),
			'eval'                    => array('multiple' => true)
		),
		'published' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxzones']['published'],
			'inputType'               => 'checkbox'
		)
  )
);

?>
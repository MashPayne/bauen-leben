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
 * @copyright  Stefan Gandlau
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_webshop_taxes
 */
$GLOBALS['TL_DCA']['tl_webshop_taxes'] = array
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
			'fields'                  => array('title', 'tax_rate'),
			'format'                  => '%s [<b>%s%%</b>]',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_webshop_taxes']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
    'default'                     => 'title,tax_rate,tax_class,tax_zone',
	),

	// Fields
	'fields' => array
	(
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['title'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
		),
		'tax_rate' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['tax_rate'],
      'inputType'               => 'text',
      'eval'                    => array('mandatory' => true, 'rgxp' => 'numeric')
    ),
		'tax_class' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['tax_class'],
			'inputType'               => 'select',
			'foreignKey'              => 'tl_webshop_taxclasses.title',
			'eval'                    => array('mandatory' => true)
		),
		'tax_zone' => array(
		  'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_taxes']['tax_zone'],
      'inputType'               => 'select',
      'foreignKey'              => 'tl_webshop_taxzones.title',
      'eval'                    => array('mandatory' => true)
		)
	)
);

?>
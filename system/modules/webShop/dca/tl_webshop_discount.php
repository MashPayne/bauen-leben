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
 * @copyright  Stefan Gandlau 2010
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_webshop_discount
 */
$GLOBALS['TL_DCA']['tl_webshop_discount'] = array(

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
      'mode'                    => 0,
      'fields'                  => array('title'),
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_discount']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_discount']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_discount']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.gif'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                 => 'title;usergroup,cartValue,shippingFree,discountType,discountValue;start,stop,active'
  ),

  // Subpalettes
  'subpalettes' => array(
    
  ),

  // Fields
  'fields' => array
  (
    'title' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['title'],
      'inputType'             => 'text',
      'eval'                  => array('mandatory' => true)
    ),
    'usergroup' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['usergroup'],
      'inputType'             => 'checkbox',
      'foreignKey'           => 'tl_member_group.name',
      'eval'                  => array('mandatory' => true, 'multiple' => true)
    ),
    'cartValue' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['cartValue'],
      'inputType'             => 'text',
      'eval'                  => array('rgxp' => 'digit')
    ),
    'shippingFree' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['shippingFree'],
      'inputType'             => 'checkbox'
    ),
    'discountType' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['discountType'],
      'inputType'             => 'select',
      'options'               => array('percent', 'value'),
      'reference'             => &$GLOBALS['TL_LANG']['tl_webshop_discount']['discountTypes'],
      'eval'                  => array('includeBlankOption' => true)
    ),
    'discountValue' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['discountValue'],
      'inputType'             => 'text',
      'eval'                  => array('rgxp' => 'digit')
    ),
    'active' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['active'],
      'inputType'             => 'checkbox'
    ),
    'start' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['start'],
      'inputType'             => 'text',
      'eval'                  => array('rgxp' => 'date', 'datepicker' => $this->getDatePickerstring(), 'tl_class' => 'w50')
    ),
    'stop' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_discount']['stop'],
      'inputType'             => 'text',
      'eval'                  => array('rgxp' => 'date', 'datepicker' => $this->getDatePickerstring(), 'tl_class' => 'w50')
    )
  )
);


?>
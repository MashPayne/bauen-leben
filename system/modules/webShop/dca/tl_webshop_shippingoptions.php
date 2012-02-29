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
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_webshop_shippingoptions
 */
$GLOBALS['TL_DCA']['tl_webshop_shippingoptions'] = array
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
      'fields'                  => array('title', 'shippingZones'),
      'label_callback'          => array('tl_webshop_shippingoptions', 'formatLabel')
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('shippingPriceType'),
    'default'                     => 'title,shippingPriceType;published',
    'shippingByWeight'            => 'title,shippingPriceType,shippingPricesWeight;shippingZones,shippingTax;published',
    'shippingByPrice'             => 'title,shippingPriceType,shippingPricesPrice;shippingZones,shippingTax;published',
    'shippingInfo'                => 'title,shippingPriceType,shippingInfo;shippingZones;published'
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
    ),
    'shippingPriceType' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingPriceType'],
      'inputType'               => 'select',
      'options'                 => array('shippingByWeight', 'shippingByPrice', 'shippingInfo'),
      'reference'               => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingPriceTypes'],
      'default'                 => 'shippingByWeight',
      'eval'                    => array('submitOnChange' => true)
    ),
    'shippingPricesWeight' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingPricesWeight'],
      'inputType'               => 'optionWizard',
      'eval'                    => array('mode' => 'byWeight')
    ),
    'shippingPricesPrice' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingPricesPrice'],
      'inputType'               => 'optionWizard',
      'eval'                    => array('mode' => 'byPrice')
    ),
    'shippingTax' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingTax'],
      'inputType'               => 'select',
      'foreignKey'              => 'tl_webshop_taxclasses.title',
      'eval'                    => array('includeBlankOption' => true)
    ),
    'shippingInfo' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingText'],
      'inputType'               => 'textarea',
      'eval'                    => array('rte' => 'tinyMCE')
    ),
    'shippingZones' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['shippingZones'],
      'inputType'               => 'checkbox',
      'foreignKey'              => 'tl_webshop_shippingzones.title',
      'eval'                    => array('multiple' => true)
    )
  )
);

class tl_webshop_shippingoptions extends Backend {
  
  public function formatLabel($row, $label) {
    $arrZones = array();
    $arrIDs = deserialize($row['shippingZones']);
    if(is_array($arrIDs) && count($arrIDs)) {
    $res = $this->Database->prepare('SELECT * from tl_webshop_shippingzones where id in ('. join(', ', $arrIDs) .')')->execute();
    if($res->numRows > 0)
      while($res->next())
        $arrZones[] = $res->title;
    } else
      $arrZones = array($GLOBALS['TL_LANG']['tl_webshop_shippingoptions']['zones_empty']);
    
    return(sprintf('<b>%s</b> [%s]', $row['title'], join(', ', $arrZones)));
  }
  
}

?>
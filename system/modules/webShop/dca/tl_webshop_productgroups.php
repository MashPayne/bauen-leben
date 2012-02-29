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
 * @copyright  Stefan Gandlaz 2009
 * @author     Stefan Gandlau <stefan@gandlau.net> 
 * @package    webShop
 * @license    LGPL 
 * @filesource
 */


/**
 * Table tl_webshop_productgroups 
 */
$GLOBALS['TL_DCA']['tl_webshop_productgroups'] = array
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
      'flag'                    => 1,
      'panelLayout'             => 'search,limit'
    ),
    'label' => array
    (
      'fields'                  => array('title'),
      'format'                  => '%s'
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('addImage'),
    'default'                     => 'title,pagetitle,alias,descriptiontext,addImage;keywords,description'
  ),
  
  'subpalettes' => array(
    'addImage'                    => 'singleSRC'
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['title'],
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
    ),
    'alias' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128),
      'save_callback'           => array(array('tl_webshop_productgroups', 'generateAlias'))
    ),
    'addImage' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['addImage'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'singleSRC' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['singleSRC'],
      'inputType'               => 'fileTree',
      'eval'                    => array('mandatory' => true, 'fieldType' => 'radio', 'files' => true, 'filesOnly' => true, 'extensions' => 'jpg,jpeg,png,gif')
    ),
    'description' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['description'],
      'inputType'               => 'textarea',
      'eval'                    => array('rows' => 3, 'style' => 'height: 60px;')
    ),
    'keywords' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['keywords'],
      'inputType'               => 'textarea',
      'eval'                    => array('rows' => 3, 'style' => 'height: 60px;')
    ),
    'descriptiontext' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['descriptiontext'],
    	'inputType'				=> 'textarea',
    	'eval'					=> array('rte' => 'tinyMCE')
    ),
    'pagetitle' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_productgroups']['pagetitle'],
    	'inputType'				=> 'text'
    )
  )
);

class tl_webshop_productgroups extends Backend {
  
  public function generateAlias($varValue, DataContainer $dc) {
    $autoAlias = false;

    if (!strlen($varValue)) {
      $objTitle = $this->Database->prepare("SELECT title FROM tl_webshop_productgroups WHERE id=?")->limit(1)->execute($dc->id);
      $autoAlias = true;
      $varValue = standardize($objTitle->title);
    }

    $objAlias = $this->Database->prepare("SELECT id FROM tl_webshop_productgroups WHERE id=? OR alias=?")->execute($dc->id, $varValue);

    if ($objAlias->numRows > 1) {
      if (!$autoAlias)
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));

      $varValue .= '.' . $dc->id;
    }
    return $varValue;
  }
    
}

?>
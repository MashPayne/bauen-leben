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
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_webshop_categories'] = array
(

  // Config
  'config' => array
  (
    'label'                       => &$GLOBALS['TL_LANG']['tl_webshop_categories']['webShopTitle'],
    'dataContainer'               => 'Table',
    'ctable'                      => array('tl_webshop_article'),
    'enableVersioning'            => true,
    'switchToEdit'                => true
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 5,
      'icon'                    => 'pagemounts.gif',
    ),
    'label' => array
    (
      'fields'                  => array('title'),
      'format'                  => '%s',
      'label_callback'          => array('tl_webshop_categories', 'addImage')
    ),
    'global_operations' => array
    (
      'updateAttributes' => array(
    	'label'				  => &$GLOBALS['TL_LANG']['tl_webshop_categories']['updateAttributes'],
    	'href'				  => 'key=updateAttributes',
    	'class'				  => 'header_update_attributes',
    	'attributes'		  => 'onclick="Backend.getScrollOffset();"'
      ),
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset();"'
      ),
      'toggleNodes' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleNodes'],
        'href'                => 'ptg=all',
        'class'               => 'header_toggle'
      )
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['edit'],
        'href'                => 'table=tl_webshop_article',
        'icon'                => 'edit.gif'
    ),

    'copy' => array
    (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['copy'],
        'href'                => 'act=paste&amp;mode=copy',
        'icon'                => 'copy.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.gif'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('type', 'showCategories', 'protected', 'showTeaser'),
    'category'                    => '{lbl_type},title,alias,type,pagetitle;{lbl_details},showTeaser,categoryImage,showCategories,sortable;{lbl_seo},categoryDescription,categoryKeywords;{lbl_protection},protected,{lbl_display},hide,cssClass;start,stop,published',
    'latest'                      => '{lbl_type},title,alias,type,pagetitle;{lbl_details},showTeaser;linkTarget,sortable;{lbl_display},hide,cssClass;start,stop,published',
    'offerMarked'                 => '{lbl_type},title,alias,type,pagetitle;{lbl_details},showTeaser;linkTarget,sortable;{lbl_display},hide,cssClass;start,stop,published',
    'newMarked'                   => '{lbl_type},title,alias,type,pagetitle;{lbl_details},showTeaser;linkTarget,sortable;{lbl_display},hide,cssClass;start,stop,published',
    'forward'                     => '{lbl_type},title,alias,type;{lbl_details},linkTarget;{lbl_display},hide,cssClass;start,stop,published',
    'page'                        => '{lbl_type},title,alias,type;{lbl_details},tl_page;{lbl_display},hide,cssClass;start,stop,published'
    
  ),

  // Subpalettes
  'subpalettes' => array
  (
    'showCategories'              => 'categoryTemplate',
    'protected'                   => 'groups',
    'showTeaser'                  => 'teaser'
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'decodeEntities'=>true)
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128),
      'save_callback'           => array(array('tl_webshop_categories', 'generateAlias'))
    ),
    'type' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['type'],
      'default'                 => 'category',
      'exclude'                 => true,
      'inputType'               => 'select',
      'options'                 => array('category', 'forward', 'latest', 'newMarked', 'offerMarked', 'page'),
      'eval'                    => array('submitOnChange'=>true),
      'reference'               => &$GLOBALS['TL_LANG']['tl_webshop_categories']['categoryTypes']
    ),
    'protected' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['protected'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'groups' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['groups'],
      'inputType'               => 'checkbox',
      'foreignKey'              => 'tl_member_group.name',
      'eval'                    => array('multiple' => true)
    ),    
    'hide' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['hide'],
      'inputType'               => 'checkbox'
    ),
    'start' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['start'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString(), 'tl_class' => 'w50 wizard')
    ),
    'stop' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['stop'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString(), 'tl_class' => 'w50 wizard')
    ),
    'published' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['published'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('doNotCopy'=>true)
    ),
    'linkTarget' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['linkTarget'],
      'inputType'               => 'shopTree',
      'eval'                    => array('mandatory' => true, 'fieldType' => 'radio')
    ),
    'showCategories' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['showCategories'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'categoryTemplate' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['categoryTemplate'],
      'inputType'               => 'select',
      'options'                 => $this->getTemplateGroup('webShop_categorylist_'),
      'eval'                    => array('mandatory' => true)
    ),
    'categoryImage' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['categoryImage'],
      'inputType'               => 'fileTree',
      'eval'                    => array('files' => true, 'filesOnly' => true, 'extensions' => 'jpg,png,gif,jpeg', 'fieldType' => 'radio')
    ),
    'categoryDescription' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['categoryDescription'],
      'inputType'               => 'textarea',
      'eval'                    => array('rows' => 3, 'style' => 'height: 60px;')
    ),
    'categoryKeywords' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['categoryKeywords'],
      'inputType'               => 'textarea',
      'eval'                    => array('rows' => 3, 'style' => 'height: 60px;')
    ),
    'showTeaser' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['showTeaser'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'teaser' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['teaser'],
      'inputType'               => 'textarea',
      'eval'                    => array('rte' => 'tinyMCE', 'mandatory' => true)
    ),
    'sortable' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['sortable'],
      'inputType'               => 'checkbox'
    ),
    'cssClass' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['cssClass'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>64)
    ),
    'tl_page' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_categories']['tl_page'],
      'inputType'               => 'pageTree',
      'eval'                    => array('fieldType' => 'radio', 'mandatory' => true)
    ),
    'pagetitle' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_categories']['pagetitle'],
    	'inputType'				=> 'text'
    )
  )
);

class tl_webshop_categories extends Backend {

  public function generateAlias($varValue, DataContainer $dc)
  {
    $autoAlias = false;

    // Generate alias if there is none
    if (!strlen($varValue))
    {
      $objTitle = $this->Database->prepare("SELECT title FROM tl_webshop_categories WHERE id=?")
                     ->limit(1)
                     ->execute($dc->id);

      $autoAlias = true;
      $varValue = standardize($objTitle->title);
    }

    $objAlias = $this->Database->prepare("SELECT id FROM tl_webshop_categories WHERE id=? OR alias=?")
                   ->execute($dc->id, $varValue);

    // Check whether the page alias exists
    if ($objAlias->numRows > 1) {
      if (!$autoAlias)
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
      $varValue .= '.' . $dc->id;
    }
    return $varValue;
  }
  
  public function addImage($row, $label, DataContainer $dc, $imageAttribute, $blnReturnImage=false) {
    $res = $this->Database->prepare("SELECT * from tl_webshop_categories where id=?")->execute($row['id']);
    $row = $res->fetchAssoc();
    $sub = 0;
    $image = ''.$row['type'].'.png';
    // array('category', 'forward', 'latest', 'newMarked', 'offerMarked', 'page')
    if(!in_array($row['type'], array('bestseller', 'page'))) {
      // Page not published or not active
      if ((!$row['published'] || $row['start'] && $row['start'] > time() || $row['stop'] && $row['stop'] < time())) {
        $sub += 1;
      }
  
      // Page hidden from menu
      if ($row['hide']) {
        $sub += 2;
      }
  
      // Page protected
      if ($row['protected']) {
        $sub += 4;
      }
  
      // Get image name
      if ($sub > 0) {
        $image = ''.$row['type'].'_'.$sub.'.png';
      }
      $image = 'system/modules/webShop/html/icons/'. $image;
    } else {
      if($row['type'] == 'page')
        $image = '/system/themes/default/images/regular.gif';

    }
    

    
    // Return image
    return $this->generateImage($image, '', $imageAttribute).' '.$label;
  }
}

?>
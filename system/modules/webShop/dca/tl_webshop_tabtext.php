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
 * @package    tl_webshop_tabtext
 * @license    LGPL
 * @filesource
 */


/**
 * Table 
 */
$GLOBALS['TL_DCA']['tl_webshop_tabtext'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'enableVersioning'            => true,
    'ptable'                      => 'tl_webshop_article'
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('sorting'),
      'panelLayout'             => 'filter;search,limit',
      'headerFields'            => array('title'),
      'child_record_callback'   => array('tl_webshop_tabs', 'listTabs')
    ),
    'label' => array
    (
      'fields'                  => array('headline'),
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['copy'],
        'href'                => 'act=paste&amp;mode=copy',
        'icon'                => 'copy.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"',
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.gif'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                 => 'headline,text'
  ),

  // Subpalettes
  'subpalettes' => array(
    
  ),

  // Fields
  'fields' => array
  (
    'headline' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['headline'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'inputUnit',
      'options'                 => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
      'eval'                    => array('maxlength'=>255)
    ),
    'text' => array(
      'label'                 => &$GLOBALS['TL_LANG']['tl_webshop_tabtext']['text'],
      'inputType'             => 'textarea',
      'eval'                  => array('mandatory' => true, 'rte' => 'tinyMCE')
    )
  )
);

class tl_webshop_tabs extends Backend {

  public function listTabs($row) {
    $arrHeadline = deserialize($row['headline']);
    if(is_array($arrHeadline))
      $strHeadline = sprintf('<%s>%s</%s>', $arrHeadline['unit'], $arrHeadline['value'], $arrHeadline['unit']);
    return(sprintf('<div class="cte_text">%s<div class="tabtext">%s</div></div>', $strHeadline, $row['text']));
  }

}

?>
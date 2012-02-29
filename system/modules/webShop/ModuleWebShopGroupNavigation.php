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
 * @license    EULA 
 * @filesource
 */


/**
 * Class ModuleWebShopGroupNavigation
 * Provides methodes to generate productgroup navigation
 * 
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */


  class ModuleWebShopGroupNavigation extends Module {
    
    protected $strTemplate = 'webshop_groupnavigation';
    
    public function generate() {
      if(TL_MODE == 'BE') {
        $t = new BackendTemplate('be_wildcard');
        $t->wildcard = '### Product Group Navigation ###';
        return($t->parse());
      }
      return(parent::generate());
    }
    
    protected function compile() {
      /* check for groups to display */
      $arrDisplayGroups = deserialize($this->webShop_showWSPGroups);
      
      /* nothing selected, return */
      if(!is_array($arrDisplayGroups) || $arrDisplayGroups[0] == '') return;
      
      /* configuration missing, return */
      if(!is_numeric($GLOBALS['TL_CONFIG']['webShop_jumpToCategory'])) return;
      
      $selectedGroup = $this->Input->get('group');
      
      /* get target page data for frontendurl */
      $res = $this->Database->prepare('SELECT * from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCategory']);
      
      /* articlelist page not found, return */
      if($res->numRows == 0) return;
      $arrJump = $res->fetchAssoc();
      
      /* query all selected productgroups */
      $res = $this->Database->prepare('SELECT * from tl_webshop_productgroups where id IN ('. join(',', $arrDisplayGroups) .') ORDER BY title')->execute();
      
      /* nothing found, return */
      if($res->numRows == 0)  return;
      
      /* image dimensions */
      if($this->webShop_asImage)
        $size = deserialize($this->size);
      
      /* loop through all groups and generate links */
      $arrItems = array();
      while($res->next()) {
        $arrItems[] = array(
          'title' => $res->title,
          'href' => $this->generateFrontendUrl($arrJump, '/'. $GLOBALS['webShop']['groupKeyword'] .'/'. $res->alias),
          'css' => $res->alias == $selectedGroup ? 'active' : '',
          'image' => $this->webShop_asImage && $res->addImage ? ($res->singleSRC != '' ? $this->getImage($res->singleSRC, $size[0], $size[1]) : false) : false
        );
      }

      /* set extra css classes */
      $arrItems[0]['css'] .= ' first';
      $arrItems[count($arrItems) - 1]['css'] .= ' last';

      /* pass to template */
      $this->Template->items = $arrItems;
    }
    
  }

?>
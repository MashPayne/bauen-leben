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
 * @copyright  Stefan Gandlau 2008
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 * @license    EULA
 * @filesource
 */


/**
 * Class wdgShopTree
 *
 * Provide methods to handle input field "shopTree".
 * @copyright  Stefan Gandlau 2008
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */
class wdgShopTree extends Widget
{

  /**
   * Submit user input
   * @var boolean
   */
  protected $blnSubmitInput = true;

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'be_widget';


  /**
   * Load database object
   * @param array
   */
  public function __construct($arrAttributes=false)
  {
    $this->import('Database');
    parent::__construct($arrAttributes);
    if(TL_MODE == 'BE')
      $GLOBALS['TL_JAVASCRIPT']['webShopBE'] = 'system/modules/webShop/html/webShopBE.js';
        
  }


  /**
   * Add specific attributes
   * @param string
   * @param mixed
   */
  public function __set($strKey, $varValue)
  {
    switch ($strKey)
    {
      case 'mandatory':
        $this->arrConfiguration['mandatory'] = $varValue ? true : false;
        break;

      default:
        parent::__set($strKey, $varValue);
        break;
    }
  }


  /**
   * Skip the field if "change selection" is not checked
   * @param mixed
   * @return mixed
   */
  protected function validator($varInput)
  {
    if (!$this->Input->post($this->strName.'_save'))
    {
      $this->mandatory = false;
      $this->blnSubmitInput = false;
    }

    return parent::validator($varInput);
  }


  /**
   * Generate the widget and return it as string
   * @return string
   */
  public function generate()
  {
    $this->import('BackendUser', 'User');
    $tree = '';

    // Show all pages to admins

    $objPage = $this->Database->prepare("SELECT id FROM tl_webshop_categories WHERE pid=? ORDER BY sorting")
                  ->execute(0);

    while ($objPage->next())
    {
      $tree .= $this->renderPagetree($objPage->id, -20);
    }

    $strReset = '';

    // Reset radio button selection
    if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'radio')
    {
      $strReset = "\n" . '    <li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="ctrl_'.$this->strId.'_0" class="tl_change_selected">'.$GLOBALS['TL_LANG']['MSC']['resetSelected'].'</label> <input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_0" class="tl_tree_radio" value="" onfocus="Backend.getScrollOffset();" /></div><div style="clear:both;"></div></li>';
    }

    // Return the tree
    return '  <ul class="tl_listing tl_webshop_categories'.(strlen($this->strClass) ? ' ' . $this->strClass : '').'" id="'.$this->strId.'">
    <li class="tl_folder_top"><div class="tl_left">'.$this->generateImage((strlen($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['icon']) ? $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['icon'] : 'pagemounts.gif')).' Kategoriebaum</div> <div class="tl_right"><label for="ctrl_'.$this->strId.'" class="tl_change_selected">'.$GLOBALS['TL_LANG']['MSC']['changeSelected'].'</label> <input type="checkbox" name="'.$this->strName.'_save" id="ctrl_'.$this->strId.'" class="tl_tree_checkbox" value="1" onclick="Backend.showTreeBody(this, \''.$this->strId.'_parent\');" /></div><div style="clear:both;"></div></li><li class="parent" id="'.$this->strId.'_parent"><ul>'.$tree.$strReset.'
  </ul></li></ul>';
  }


  /**
   * Generate a particular subpart of the page tree and return it as HTML string
   * @param integer
   * @param string
   * @param integer
   * @return string
   */
  public function generateAjax($id, $strField, $level)
  {
    if (!$this->Input->post('isAjax'))
    {
      return '';
    }

    $this->strField = $strField;
    $this->loadDataContainer($this->strTable);

    // Load current values
    switch ($GLOBALS['TL_DCA'][$this->strTable]['config']['dataContainer'])
    {
      case 'File':
        if (strlen($GLOBALS['TL_CONFIG'][$this->strField]))
        {
          $this->varValue = $GLOBALS['TL_CONFIG'][$this->strField];
        }
        break;

      case 'Table':
        if (!$this->Database->fieldExists($strField, $this->strTable))
        {
          break;
        }

        $objField = $this->Database->prepare("SELECT " . $strField . " FROM " . $this->strTable . " WHERE id=?")
                       ->limit(1)
                       ->execute($this->strId);

        if ($objField->numRows)
        {
          $this->varValue = deserialize($objField->$strField);
        }
        break;
    }

    // Load requested nodes
    $tree = '';
    $level = $level * 20;

    $objPage = $this->Database->prepare("SELECT id FROM tl_webshop_categories WHERE pid=? ORDER BY sorting")
                  ->execute($id);

    while ($objPage->next())
    {
      $tree .= $this->renderPagetree($objPage->id, $level);
    }
    if($GLOBALS['TL_DCA'][$this->strTable]['fields'][$strField]['eval']['showArticle']) {
      $items = $this->readItemDB($id);
      if(count($items))
        foreach($items as $i => $item)
          $tree .= $this->renderItems($item['id'], $level);    
    }
    return $tree;
  }


  /**
   * Recursively render the pagetree
   * @param int
   * @param integer
   * @param boolean
   * @return string
   */
  protected function renderPagetree($id, $intMargin, $protectedPage=false)
  {
    static $session;
    $session = $this->Session->getData();

    $flag = substr($this->strField, 0, 2);
    $node = 'tree_' . $this->strTable . '_' . $this->strField;
    $xtnode = 'tree_' . $this->strTable . '_' . $this->strName;

    // Get session data and toggle nodes
    if ($this->Input->get($flag.'tg'))
    {
      $session[$node][$this->Input->get($flag.'tg')] = (isset($session[$node][$this->Input->get($flag.'tg')]) && $session[$node][$this->Input->get($flag.'tg')] == 1) ? 0 : 1;
      $this->Session->setData($session);

      $this->redirect(preg_replace('/(&(amp;)?|\?)'.$flag.'tg=[^& ]*/i', '', $this->Environment->request));
    }

    $objPage = $this->Database->prepare("SELECT id, type, start, stop, hide, protected, published, title FROM tl_webshop_categories WHERE id=?")
                  ->limit(1)
                  ->execute($id);

    // Return if there is no result
    if ($objPage->numRows < 1)
    {
      return '';
    }

    $return = '';
    $intSpacing = 20;
    $childs = array();

    // Check whether there are child records
    $objNodes = $this->Database->prepare("SELECT id FROM tl_webshop_categories WHERE pid=? ORDER BY sorting")
                   ->execute($id);

    if ($objNodes->numRows)
    {
      $childs = $objNodes->fetchEach('id');
    }
    $items = $this->readItemDB($id);
    
    $return .= "\n    " . '<li class="'.(($objPage->type == 'category') ? 'shop_cat' : 'shop_link').'" onmouseover="Theme.hoverDiv(this, 1);" onmouseout="Theme.hoverDiv(this, 0);"><div class="tl_left" style="padding-left:'.($intMargin + $intSpacing).'px;">';

    $folderAttribute = 'style="margin-left:20px;"';
    $session[$node][$id] = is_numeric($session[$node][$id]) ? $session[$node][$id] : 0;
    $level = ($intMargin / $intSpacing + 1);

    if (count($childs) || (count($items) && $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['showArticle']))
    {
      $folderAttribute = '';
      $img = ($session[$node][$id] == 1) ? 'folMinus.gif' : 'folPlus.gif';
      $return .= '<a href="'.$this->addToUrl($flag.'tg='.$id).'" onclick="Backend.getScrollOffset(); return webShopAjax.toggleCategoryTree(this, \''.$id.'\', \''.$this->strField.'\', \''.$this->strName.'\', '.$level.');">'.$this->generateImage($img, '', 'style="margin-right:2px;"').'</a>';
    }

    $sub = 0;
    $image = ''.$objPage->type.'.png';
    if(!in_array($objPage->type, array('forward', 'bestseller', 'latest'))) {
      // Page not published or not active
      if ((!$objPage->published || $objPage->start && $objPage->start > time() || $objPage->stop && $objPage->stop < time())) {
        $sub += 1;
      }
  
      // Page hidden from menu
      if ($objPage->hide) {
        $sub += 2;
      }
  
      // Page protected
      if ($objPage->protected) {
        $sub += 4;
      }
  
      // Get image name
      if ($sub > 0) {
        $image = ''.$objPage->type.'_'.$sub.'.png';
      }
    }
    $image = 'system/modules/webShop/html/icons/'. $image;

    // Add page name
    $return .= $this->generateImage($image, '', $folderAttribute).' <label for="'.$this->strName.'_'.$id.'">'. $objPage->title .'</label></div> <div class="tl_right">';

    // Add checkbox or radio button
      if(!$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['showArticle']) {
      switch ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'])
      {
        case 'checkbox':
          $return .= '<input type="checkbox" name="'.$this->strName.'[]" id="'.$this->strName.'_'.$id.'" class="tl_tree_checkbox" value="'.specialchars($id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($id, $this->varValue).' />';
          break;
  
        case 'radio':
          $return .= '<input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_'.$id.'" class="tl_tree_radio" value="'.specialchars($id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($id, $this->varValue).' />';
          break;
      }
    }
    $return .= '</div><div style="clear:both;"></div></li>';

  
    // Begin new submenu
    if (count($childs) && $session[$node][$id] == 1 || ((count($items) && $session[$node][$id] == 1) && $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['showArticle']))
    {
      $return .= '<li class="parent" id="'.$node.'_'.$id.'"><ul class="level_'.$level.'">';

      if(count($childs)) {
        for ($k=0; $k<count($childs); $k++)
        {
          $return .= $this->renderPagetree($childs[$k], ($intMargin + $intSpacing), $objPage->protected);
        }
      }
      if($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['showArticle']) {
        for ($k=0; $k<count($items); $k++)
        {
          $return .= $this->renderItems($items[$k]['id'], ($intMargin + $intSpacing), $objPage->protected);
        }
      }

      $return .= '</ul></li>';
    }

    return $return;
  }
  
    /**
   * Recursively render the pagetree
   * @param int
   * @param integer
   * @param boolean
   * @return string
   */
  protected function renderItems($id, $intMargin, $protectedPage=false)
  {
    static $session;
    $session = $this->Session->getData();

    $flag = substr($this->strField, 0, 2);
    $node = 'tree_' . $this->strTable . '_' . $this->strField;
    $xtnode = 'tree_' . $this->strTable . '_' . $this->strName;

    // Get session data and toggle nodes
    if ($this->Input->get($flag.'tg'))
    {
      $session[$node][$this->Input->get($flag.'tg')] = (isset($session[$node][$this->Input->get($flag.'tg')]) && $session[$node][$this->Input->get($flag.'tg')] == 1) ? 0 : 1;
      $this->Session->setData($session);

      $this->redirect(preg_replace('/(&(amp;)?|\?)'.$flag.'tg=[^& ]*/i', '', $this->Environment->request));
    }

    $objPage = $this->Database->prepare("SELECT id, type, published, start, stop, title, productid FROM tl_webshop_article WHERE id=?")
                  ->limit(1)
                  ->execute($id);

    // Return if there is no result
    if ($objPage->numRows < 1)
    {
      return '';
    }

    $return = '';
    $intSpacing = 20;
    $childs = array();

    // Check whether there are child records

    $return .= "\n    " . '<li class="'.(($objPage->type == 'category') ? 'shop_cat' : 'shop_link').'" onmouseover="Theme.hoverDiv(this, 1);" onmouseout="Theme.hoverDiv(this, 0);"><div class="tl_left" style="padding-left:'.($intMargin + $intSpacing).'px;">';

    $folderAttribute = 'style="margin-left:20px;"';
    $session[$node][$id] = is_numeric($session[$node][$id]) ? $session[$node][$id] : 0;
    $level = ($intMargin / $intSpacing + 1);

    if(!$this->isPublished($objPage->published, $objPage->start, $objPage->stop))
		  $image = array('article_offline.png', '');
		else {
			if($objPage->type == 'articleVariants')
			  $image = array('article_variante.png', '');
			else
			  $image = array('article_einfach.png', '');
		}

		
   
    // Add page name
    $return .= $this->generateImage('system/modules/webShop/html/icons/'. $image[0], '', $folderAttribute .' title="'. $image[1] .'"').' <label for="'.$this->strName.'_'.$id.'">'. $objPage->title .' ('. $objPage->productid .')</label></div> <div class="tl_right">';

    // Add checkbox or radio button
    switch ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'])
    {
      case 'checkbox':
        $return .= '<input type="checkbox" name="'.$this->strName.'[]" id="'.$this->strName.'_'.$id.'" class="tl_tree_checkbox" value="'.specialchars($id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($id, $this->varValue).' />';
        break;

      case 'radio':
        $return .= '<input type="radio" name="'.$this->strName.'" id="'.$this->strName.'_'.$id.'" class="tl_tree_radio" value="'.specialchars($id).'" onfocus="Backend.getScrollOffset();"'.$this->optionChecked($id, $this->varValue).' />';
        break;
    }

    $return .= '</div><div style="clear:both;"></div></li>';

    
    return $return;
  }
  
  protected function readItemDB($id) {
    $res = $this->Database->prepare("SELECT * from tl_webshop_article where pid=?")->execute($id);
    if($res->numRows > 0) {
      return($res->fetchAllAssoc());
    }
    
    return(array());
  }
	
	protected function isPublished($published, $start, $stop) {
		if($published == '') return(false);
		if(($start == '' || $start < time()) && ($stop == '' || $stop > time()))
		  return(true);
		return(false);
	}
}

?>
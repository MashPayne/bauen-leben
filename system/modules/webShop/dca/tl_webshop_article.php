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
 * Table tl_webshop_article
 */
  
require_once(TL_ROOT .'/system/modules/webShop/functions.php');

$GLOBALS['TL_DCA']['tl_webshop_article'] = array(

  // Config
  'config' => array
  (
    'dataContainer'             => 'Table',
    'ptable'                    => 'tl_webshop_categories',
    'switchToEdit'              => false,
    'enableVersioning'          => true,
    'onsubmit_callback'         => array(
                                    array('webShop', 'generateExports'),
                                    array('webShop', 'updateProductGroups')
                                   )
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('sorting'),
      'headerFields'            => array('title', 'type', 'published', 'start', 'stop'),
      'child_record_callback'   => array('tl_webshop_article', 'listArticle'),
      'panelLayout'             => 'sort,filter;search,limit'
    ),
    'label' => array
    (
      'fields'                  => array('title'),
      'format'                  => '%s',
      'label_callback'          => array('tl_webshop_article', 'addImage')
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
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['copy'],
        'href'                => 'act=paste&amp;mode=copy',
        'icon'                => 'copy.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"',
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset();"',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
      ),
      
      'tabtext' => array(
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['tabtext'],
        'href'                => 'table=tl_webshop_tabtext',
        'icon'                => 'system/modules/webShop/html/icons/tabtext.png',
        'button_callback'     => array('tl_webshop_article', 'editTabs')
      ),
      'toggle' => array(
		'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['toggle'],
		'icon'                => 'visible.gif',
		'attributes'          => 'onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this, %s);"',
		'button_callback'     => array('tl_webshop_article', 'toggleIcon')
	  ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.gif'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('type', 'addImage', 'addGallery', 'addStock', 'showvpe'),
    'article'                     => '{lbl_type},title,alias,type;{lbl_description},productid,teaser,description,tags,html;{lbl_seo:hide},keywords,seoDescription;{lbl_details:hide},weight,productgroup,deliveryTime,specialoffer,isnew,added,allowComment;{lbl_stock:hide},addStock;{lbl_images:hide},addImage,addGallery;{lbl_prices},singlePrice,singlePrice2,taxid,showvpe;{lbl_specialprices:hide},specialprice,specialprice_start,specialprice_stop,groupPrices;{lbl_recommendet:hide},recommendet;{lbl_display},published,start,stop'
  ),

  // Subpalettes
  'subpalettes' => array(
    'addImage'                    => 'singleSRC',
    'addGallery'                  => 'multiSRC',
    'addStock'                    => 'stock,hideIfEmpty',
    'showvpe'                     => 'vpeid,vpefactor',
  ),

  // Fields
  'fields' => array
  (
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128),
      'save_callback'           => array(array('tl_webshop_article', 'generateAlias'))
    ),
    'published' => array
    (
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['published'],
      'inputType'               => 'checkbox',
      'eval'                    => array('doNotCopy'=>true),
      'filter'                  => true
    ),
    'teaser' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['teaser'],
      'inputType'               => 'textarea',
      'eval'                    => array('row' => 3, 'style' => 'height: 60px;')
    ),
    'seoDescription' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['seoDescription'],
      'inputType'               => 'textarea',
      'eval'                    => array('rows' => 3, 'style' => 'height: 60px;')
    ),
    'description' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['description'],
      'inputType'               => 'textarea',
      'eval'                    => array('rte' => 'tinyMCE')
    ),
    'keywords' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['keywords'],
      'inputType'               => 'textarea',
      'eval'                    => array('row' => 3, 'style' => 'height: 60px;')
    ),
    'singlePrice' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['singlePrice'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'numeric')
    ),
    'taxid' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['taxid'],
      'inputType'               => 'select',
      'foreignKey'              => 'tl_webshop_taxclasses.title',
      'default'                 => &$GLOBALS['TL_CONFIG']['webShop_defaultTax']
    ),
    'type' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['type'],
      'inputType'               => 'select',
      'options'                 => array('article', 'forward'),
      'default'                 => 'article',
      'reference'               => &$GLOBALS['TL_LANG']['tl_webshop_article']['types'],
      'eval'                    => array('submitOnChange' => true),
      'filter'                  => true
    ),
    'start' => array
    (
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['start'],
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString())
    ),
    'stop' => array
    (
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['stop'],
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString())
    ),
    'addImage' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['addImage'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true),
      'filter'                  => true
    ),
    'addGallery' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['addGallery'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'singleSRC' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['singleSRC'],
      'inputType'               => 'fileTree',
      'eval'                    => array('files' => true, 'filesOnly' => true, 'mandatory' => true, 'extension' => 'jpg,jpeg,png,gif', 'fieldType' => 'radio')
    ),
    'multiSRC' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['multiSRC'],
      'inputType'               => 'fileTree',
      'eval'                    => array('files' => true, 'filesOnly' => true, 'mandatory' => true, 'extension' => 'jpg,jpeg,png,gif', 'fieldType' => 'checkbox', 'multiple' => true)
    ),
    'template' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['template'],
      'inputType'               => 'select',
      'options'                 => $this->getTemplateGroup('webShop_articledetails_')
    ),
    'groupPrices' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['groupPrices'],
      'inputType'               => 'groupPrices',
    ),
    'linkTarget' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['linkTarget'],
      'inputType'               => 'shopTree',
      'eval'                    => array('showArticle' => true, 'mandatory' => true, 'fieldType' => 'radio')
    ),
    'productid' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['productid'],
      'inputType'               => 'text',
      'eval'                    => array('unique' => true, 'mandatory' => true)
    ),
    'addStock' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['addStock'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'stock' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['stock'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'digit')
    ),
    'hideIfEmpty' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['hideIfEmpty'],
      'inputType'               => 'checkbox'
    ),
    'weight' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['weight'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'numeric')
    ),
    'added' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['added'],
      'inputType'               => 'text',
      'eval'                    => array('mandatory' => true, 'rgxp' => 'date', 'datepicker' => $this->getDatePickerString()),
      'filter'                  => true,
      'flag'                    => 5
    ),
    'recommendet' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['recommendet'],
      'inputType'               => 'shopTree',
      'eval'                    => array('fieldType' => 'checkbox', 'showArticle' => true)
    ),
    'isnew' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['isnew'],
      'inputType'               => 'checkbox',
      'filter'                  => true
    ),
    'specialoffer' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['specialoffer'],
      'inputType'               => 'checkbox',
      'filter'                  => true
    ),
    'specialprice' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['specialprice'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp' => 'numeric')
    ),
    'specialprice_start' => array(
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['specialprice_start'],
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString())
    ),
    'specialprice_stop' => array(
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['specialprice_stop'],
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>10, 'rgxp'=>'date', 'datepicker'=>$this->getDatePickerString())
    ),
    'deliveryTime' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['deliveryTime'],
      'inputType'               => 'text'
    ),
    'showvpe' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['showvpe'],
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange' => true)
    ),
    'vpeid' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['vpeid'],
      'inputType'               => 'select',
      'foreignKey'              => 'tl_webshop_vpe.title'
    ),
    'vpefactor' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['vpefactor'],
      'inputType'               => 'text',
      'eval'                    => array('mandatory' => true, 'rgxp' => 'digit'),
      'default'                 => '1.00000'
    ),
    'productgroup' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['productgroup'],
      'inputType'               => 'checkbox',
      'foreignKey'              => 'tl_webshop_productgroups.title',
      'eval'                    => array('multiple' =>true)
    ),
    'noqscale' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['noqscale'],
      'inputType'               => 'checkbox'
    ),
    'singlePrice2' => array(
      'label'                   => &$GLOBALS['TL_LANG']['tl_webshop_article']['singlePrice2'],
      'inputType'               => 'optionWizard',
      'eval'                    => array('rgxp' => 'numeric'),
    ),
    'tags' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_article']['tags'],
    	'inputType'				=> 'text'
    ),
    'html' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_article']['html'],
		'exclude'               => true,
		'inputType'             => 'textarea',
		'eval'                  => array('allowHtml'=>true, 'class'=>'monospace', 'style' => 'height: 60px;'),
    ),
    'allowComment' => array(
    	'label'					=> &$GLOBALS['TL_LANG']['tl_webshop_article']['allowComment'],
    	'inputType'				=> 'checkbox'
    )
  )
);


class tl_webshop_article extends Backend {

	public function __construct() {
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	public function toggleVisibility($intId, $blnVisible)  {
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'toggle');


		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_webshop_article::published', 'alexf'))
		{
			$this->log('Not enough permissions to publish/unpublish variant item ID "'.$intId.'"', 'tl_webshop_article toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_webshop_article', $intId);
	
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_webshop_article']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_webshop_article']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_webshop_article SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_webshop_article', $intId);

	}
	
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_webshop_article::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}		

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
	

  public function addImage($row, $label) {
    $published = (!strlen($row['published']) || $row['start'] && $row['start'] > time() || $row['stop'] && $row['stop'] < time()) ? false : true;
    return $this->generateImage('articles'.($published ? '' : '_').'.gif').' '.$label;
  }

  public function generateAlias($varValue, DataContainer $dc) {
    $autoAlias = false;

    if (!strlen($varValue)) {
      $objTitle = $this->Database->prepare("SELECT title FROM tl_webshop_article WHERE id=?")->limit(1)->execute($dc->id);
      $autoAlias = true;
      $varValue = standardize($objTitle->title);
    }

    $objAlias = $this->Database->prepare("SELECT id FROM tl_webshop_article WHERE id=? OR alias=?")->execute($dc->id, $varValue);

    if ($objAlias->numRows > 1) {
      if (!$autoAlias)
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));

      $varValue .= '.' . $dc->id;
    }
    return $varValue;
  }
  
  public function listArticle($arrRow) {
  
    if($arrRow['type'] == 'forward') {
      if($arrRow['linkTarget'] == 0) {
        return(sprintf('<div class="be_ws_article ws_error">%s</div>', $GLOBALS['TL_LANG']['webShop']['noLinkWarning']));
      }
      
      $res = $this->Database->prepare('SELECT * from tl_webshop_article where id=?')->execute($arrRow['linkTarget']);
      if($res->numRows == 0) {
        return(sprintf('<div class="be_ws_article ws_error">%s</div>', $GLOBALS['TL_LANG']['webShop']['wrongLinkWarning']));
      }
      
      $arrRow = $res->fetchAssoc();
      $isForward = true;
    }
    
    $objTemplate = new BackendTemplate('webShop_be_articleList');
    $objTemplate->data = $arrRow;
   
    if($arrRow['addStock'] && $arrRow['stock'] <= 0) {
   		$objTemplate->outOfStock = $GLOBALS['TL_LANG']['webShop']['outOfStock'];
    }
    
    return($objTemplate->parse());
  }
  

  public function editTabs($row, $href, $label, $title, $icon, $attributes) {
    if($row['type'] != 'forward')
      return('<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a>');
    else
      return($this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)));
  }
}

?>
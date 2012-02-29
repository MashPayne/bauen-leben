<?php

  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_navigation'] = 'name,type,headline;webShop_navigationTemplate,jumpTo,levelOffset,showLevel;hardLimit,showProtected;webShop_startPoint;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_subnavigation'] = 'name,type,headline,webShop_useParentCategoryName;webShop_navigationTemplate,jumpTo,levelOffset,showLevel;hardLimit,showProtected;webShop_startPoint;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_breadcrumb'] = 'name,type,headline;guests,webShop_navigationTemplate,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_ArticleList'] = 'name,type,headline;jumpTo;webShop_articleListTemplate,webShop_articleListItemTemplate,webShop_thumbSize,webShop_categoryImageSize,perPage;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_articleDetails'] = 'name,type,headline;jumpTo;webShop_articleTemplate,wsGallery,webShop_miniSize,webShop_thumbSize,webShop_fullSize;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_shoppingCart'] = 'name,type,headline;jumpTo,webShop_jumpLogin;webShop_cartTemplate,webShop_thumbSize;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_miniCart'] = 'name,type,headline;jumpTo;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_checkout'] = 'name,type,headline;jumpTo,webShop_jumpToAddressBook;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_checkoutConfirm'] = 'name,type,headline;jumpTo,webShop_jumpBack;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_articleRecommendet'] = 'name,type,headline;webShop_limit,webShop_thumbSize;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_groupNavigation'] = 'name,type,headline;webShop_showWSPGroups,webShop_asImage;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_articlefilter'] = 'name,type,headline;jumpTo;webShop_addFilter,webShop_addArticleList;webShop_articleListTemplate,webShop_articleListItemTemplate,webShop_thumbSize,webShop_categoryImageSize,perPage;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_tagcloud'] = 'name,type,headline;webShop_cloudLimit,webShop_cloudSizeMin,webShop_cloudSizeMax,jumpTo;guests,protected;align,space,cssID';
  $GLOBALS['TL_DCA']['tl_module']['palettes']['webShop_coupons'] = 'name,type,headline;guests,protected;align,space,cssID';
  
  $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'webShop_asImage';

  $GLOBALS['TL_DCA']['tl_module']['subpalettes']['webShop_asImage'] = 'size';

  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_addFilter'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_addFilter'],
    'inputType' => 'checkbox'
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_addArticleList'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_addArticleList'],
    'inputType' => 'checkbox'
  );
  
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_cloudLimit'] = array(
  	'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_cloudLimit'],
  	'inputType' => 'text',
  	'default' => '0',
  	'eval' => array('rgxp' => 'digit')
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_limit'] = array(
  	'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_limit'],
  	'inputType' => 'text',
  	'default' => '0',
  	'eval' => array('rgxp' => 'digit')
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_cloudSizeMax'] = array(
  	'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_cloudSizeMax'],
  	'inputType' => 'text',
  	'default' => '16',
  	'eval' => array('rgxp' => 'digit')
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_cloudSizeMin'] = array(
  	'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_cloudSizeMin'],
  	'inputType' => 'text',
  	'default' => '8',
  	'eval' => array('rgxp' => 'digit')
  );
  
  
  
  

  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_showWSPGroups'] = array(
    'label' => $GLOBALS['TL_LANG']['tl_module']['webShop_showWSPGroups'],
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_webshop_productgroups.title',
    'eval' => array('multiple' => true, 'mandatory' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_asImage'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_asImage'],
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true)
  );

  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_useParentCategoryName'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_useParentCategoryName'],
		'inputType' => 'checkbox'
	);
	
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_jumpToAddressBook'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_jumpToAddressBook'],
		'inputType' => 'pageTree',
		'eval' => array('mandatory' => true, 'fieldType' => 'radio')
	);
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_startPoint'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_startPoint'],
		'inputType' => 'shopTree',
		'eval' => array('fieldType' => 'radio')
	);
	
	$GLOBALS['TL_DCA']['tl_module']['fields']['webShop_articleListTemplate'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_articleListTemplate'],
    'inputType' => 'select',
    'options' => $this->getTemplateGroup('webShop_articlelist_'),
    'eval' => array('mandatory' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_cartTemplate'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_cartTemplate'],
    'inputType' => 'select',
    'options' => $this->getTemplateGroup('webShop_cart_'),
    'eval' => array('mandatory' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_articleListItemTemplate'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_articleListItemTemplate'],
    'inputType' => 'select',
    'options' => $this->getTemplateGroup('webShop_articlelistitem_'),
    'eval' => array('mandatory' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_navigationTemplate'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_navigationTemplate'],
    'inputType' => 'select',
    'options' => $this->getTemplateGroup('webShop_navi_'),
    'eval' => array('mandatory' => true)
  );
	
	$GLOBALS['TL_DCA']['tl_module']['fields']['webShop_articleTemplate'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_articleTemplate'],
    'inputType' => 'select',
    'options' => $this->getTemplateGroup('webShop_articledetails_'),
    'eval' => array('mandatory' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_thumbSize'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_thumbSize'],
    'inputType' => 'text',
    'eval' => array('size' => 2, 'rgxp' => 'digit', 'multiple' => true)
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_miniSize'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_miniSize'],
    'inputType' => 'text',
    'eval' => array('size' => 2, 'rgxp' => 'digit', 'multiple' => true)
  );
  
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['wsGallery'] = array(
  	'label' => &$GLOBALS['TL_LANG']['tl_module']['wsGallery'],
    'inputType' => 'select',
  	'default' => 'lightbox',
    'options' => array('lightbox', 'mojozoom'),
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['wsGalleries']
  );
  
	$GLOBALS['TL_DCA']['tl_module']['fields']['webShop_categoryImageSize'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_categoryImageSize'],
    'inputType' => 'text',
    'eval' => array('size' => 2, 'rgxp' => 'digit', 'multiple' => true)
  );
  
	$GLOBALS['TL_DCA']['tl_module']['fields']['webShop_fullSize'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_fullSize'],
    'inputType' => 'text',
    'eval' => array('size' => 2, 'rgxp' => 'digit', 'multiple' => true)
  );
	
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_jumpBack'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_jumpBack'],
    'inputType' => 'pageTree',
    'eval' => array('fieldType' => 'radio')
  );
  
  $GLOBALS['TL_DCA']['tl_module']['fields']['webShop_jumpLogin'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['webShop_jumpLogin'],
    'inputType' => 'pageTree',
    'eval' => array('fieldType' => 'radio')
  );	
	
?>
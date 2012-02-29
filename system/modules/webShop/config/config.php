<?php

  /* url keywords, feel free to change this values */

  $GLOBALS['webShop']['articleKeyword'] = 'artikel';    /* http://www.my-shop.com/articledetails/  "article"  /my-article-name.html */
  $GLOBALS['webShop']['categoryKeyword'] = 'kategorie';  /* http://www.my-shop.com/articlelist/  "category"  /lcd-tvs.html */
  $GLOBALS['webShop']['groupKeyword'] = 'gruppe';        /* http://www.my-shop.com/articlelist/  "group"  /multimedia.html */
  
  /******************************************
   * DO NOT CHANGE ANYTHING BELOW THIS LINE *
   *   UNLESS YOU KNOW WHAT YOU ARE DOING   *
   ******************************************/
  
  $GLOBALS['BE_MOD']['webShop'] = array(
    'categories' => array(
      'tables' => array('tl_webshop_categories', 'tl_webshop_article', 'tl_webshop_tabtext'),
      'icon' => 'system/modules/webShop/html/icons/category.png',
      'stylesheet' => 'system/modules/webShop/html/backend.css'
    ),
    'ws_pgroups' => array(
      'tables' => array('tl_webshop_productgroups'),
      'icon' => 'system/modules/webShop/html/icons/productgroups.png'
    ),
    'ws_vpe' => array(
      'tables' => array('tl_webshop_vpe'),
      'icon' => 'system/modules/webShop/html/icons/vpe.png'
    ),
    'coupons' => array(
      'tables' => array('tl_webshop_coupons'),
      'icon' => 'system/modules/webShop/html/icons/coupons.png'
    ),
    'orders' => array(
      'tables' => array('tl_webshop_orders'),
      'icon' => 'system/modules/webShop/html/icons/orders.png',
      'stylesheet' => 'system/modules/webShop/html/webShopBEOrders.css',
      'reminder' => array('tl_webshop_orders', 'paymentReminder')
    )
  );
  
  $GLOBALS['BE_MOD']['webShopConfig'] = array(
    'taxzones' => array(
      'tables' => array('tl_webshop_taxzones'),
      'icon' => 'system/modules/webShop/html/icons/taxzones.png',
    ),
    'taxclasses' => array(
      'tables' => array('tl_webshop_taxclasses'),
      'icon' => 'system/modules/webShop/html/icons/taxclasses.png',
    ),
    'taxes' => array(
      'tables' => array('tl_webshop_taxes'),
      'icon' => 'system/modules/webShop/html/icons/taxes.png',
    ),
    'shippingZones' => array(
      'tables' => array('tl_webshop_shippingzones'),
      'icon' => 'system/modules/webShop/html/icons/shippingzones.png',
    ),
    'shippingOptions' => array(
      'tables' => array('tl_webshop_shippingoptions'),
      'icon' => 'system/modules/webShop/html/icons/shippingoptions.png',
    ),
    'paymentOptions' => array(
      'tables' => array('tl_webshop_paymentmodules'),
      'icon' => 'system/modules/webShop/html/icons/paymentmodules.png',
    ),
    'webShopExports' => array(
      'tables' => array('tl_webshop_exports'),
      'icon' => 'system/modules/webShop/html/icons/export.png'
    ),
    'webShopDiscount' => array(
      'tables' => array('tl_webshop_discount'),
      'icon' => 'system/modules/webShop/html/icons/discount.png'
    ),
    'webShopConfiguration' => array(
      'tables' => array('tl_webshop_configuration'),
      'icon' => 'system/modules/webShop/html/icons/configuration.png',
    ),
    'webShopEmailConfig' => array(
      'tables' => array('tl_webshop_emailconfig'),
      'icon' => 'system/modules/webShop/html/icons/emailconfig.png',
    )
  );
  
  $GLOBALS['FE_MOD']['webShop'] = array(
    'webShop_navigation' => 'ModuleWebShopNavigation',
    'webShop_subnavigation' => 'ModuleWebShopNavigationSubItems',
    'webShop_ArticleList' => 'ModuleWebShopArticleList',
    'webShop_articleDetails' => 'ModuleWebShopArticleDetails',
    'webShop_articleRecommendet' => 'ModuleWebShopRecommendet',
    'webShop_checkout' => 'ModuleWebShopCheckout',
    'webShop_checkoutConfirm' => 'ModuleWebShopCheckoutConfirm',
    'webShop_orderCompleted' => 'ModuleWebShopOrderCompleted',
    'webShop_addressbook' => 'ModuleUserAddressBook',
    'webShop_myOrders' => 'ModuleWebShopMyOrders',
    'webShop_shoppingCart' => 'ModuleWebShopCart',
    'webShop_miniCart' => 'ModuleWebShopMiniCart',
    'webShop_groupNavigation' => 'ModuleWebShopGroupNavigation',
    'webShop_breadcrumb' => 'ModuleWebShopBreadcrumb',
    'webShop_tagcloud' => 'ModuleWSCloud',
    'webShop_coupons' => 'ModuleWebShopCoupons'
  );

  $GLOBALS['BE_FFL']['groupPrices'] = 'groupPriceWidget';
  $GLOBALS['BE_FFL']['shopTree'] = 'wdgShopTree';
  $GLOBALS['BE_FFL']['shippingPrices'] = 'wdgShippingOptions';
  
  $GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('webShop', 'getSearchablePages');
  $GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('webShopInsertTags', 'replaceWebShopTags');
  
  	if(!is_array($GLOBALS['TL_HOOKS']['webShopPostOrder']))
		$GLOBALS['TL_HOOKS']['webShopPostOrder'] = array();
		
	if(!is_array($GLOBALS['TL_HOOKS']['webShopOrderDetails']))
		$GLOBALS['TL_HOOKS']['webShopOrderDetails'] = array();
		
  $GLOBALS['TL_CONFIG']['webShopVersion'] = '2.3.1';
  $GLOBALS['TL_CONFIG']['webShopEdition'] = 'ce';
  
?>
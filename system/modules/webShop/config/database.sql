CREATE TABLE `tl_webshop_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `template` varchar(255) NOT NULL default '',
  `hide` char(1) NOT NULL default '',
  `protected` char(1) NOT NULL default '',
  `linkTarget` int(10) unsigned NOT NULL default '0',
  `showCategories` char(1) NOT NULL default '',
  `categoryTemplate` varchar(255) NOT NULL default '',
  `categoryImage` varchar(255) NOT NULL default '',
  `teaser` text NULL,
  `showTeaser` char(1) NOT NULL default '',
  `categoryDescription` text NULL,
  `categoryKeywords` text NULL,
  `groups` text NULL,
  `start` varchar(10) NOT NULL default '',
  `stop` varchar(10) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  `sortOrder` varchar(255) NOT NULL default '',
  `sortable` char(1) NOT NULL default '',
  `tl_page` int(10) unsigned NOT NULL default '0',
  `cssClass` varchar(255) NOT NULL default '',
  `pagetitle` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_article` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `productid` varchar(255) NOT NULL default '',
  `teaser` text NULL,
  `description` text NULL,
  `addImage` char(1) NOT NULL default '',
  `singleSRC` varchar(255) NOT NULL default '',
  `addGallery` char(1) NOT NULL default '',
  `galleryType` varchar(255) NOT NULL default '',
  `multiSRC` text NULL,
  `template` varchar(255) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  `singlePrice` float(9,4) unsigned NOT NULL default '0.0000',
  `taxid` int(10) unsigned NOT NULL default '0',
  `start` varchar(10) NOT NULL default '',
  `stop` varchar(10) NOT NULL default '',
  `groupPrices` text NULL,
  `linkTarget` int(10) unsigned NOT NULL default '0',
  `addStock` char(1) NOT NULL default '',
  `stock` int(10) NOT NULL default '0',
  `hideIfEmpty` char(1) NOT NULL default '',
  `weight` float(9,3) unsigned NOT NULL default '0.000',
  `added` int(10) unsigned NOT NULL default '0',
  `recommendet` text NULL,
  `isnew` char(1) NOT NULL default '',
  `specialoffer` char(1) NOT NULL default '',
  `specialprice` float(9,4) unsigned NOT NULL default '0.0000',
  `specialprice_start` varchar(255) NOT NULL default '',
  `specialprice_stop` varchar(255) NOT NULL default '',
  `keywords` text NULL,
  `ordercount` int(10) unsigned NOT NULL default '0',
  `seoDescription` text NULL,
  `deliveryTime` varchar(255) NOT NULL default '',
  `showvpe` char(1) NOT NULL default '',
  `vpeid` int(10) unsigned NOT NULL default '0',
  `vpefactor` varchar(32) NOT NULL default '',
  `productgroup` varchar(255) NOT NULL default '',
  `singlePrice2` text NULL,
  `noqscale` char(1) NOT NULL default '',
  `tags` varchar(255) NOT NULL default '',
  `html` text NULL,
  `allowComment` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_tabtext` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `headline` varchar(255) NOT NULL default '',
  `text` text NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_vpe` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_orders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `datim` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `shippingAddress` text NULL,
  `shippingMethodData` text NULL,
  `paymentAddress` text NULL,
  `paymentMethodData` text NULL,
  `vatid` varchar(255) NOT NULL default '',
  `coupons` text NULL,
  `billingValue` float(9,2) unsigned NOT NULL default '0.00',
  `taxes` text NULL,
  `orderStatus` varchar(255) NOT NULL default '',
  `trackingService` varchar(255) NOT NULL default '',
  `trackingID` varchar(255) NOT NULL default '',
  `payed` char(1) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `paymentModuleResponse` text NULL,
  `orderComment` text NULL,
  `email` varchar(255) NOT NULL default '',
  `paymentResponse` text NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_orderitems` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `singlePrice` float(9,2) unsigned NOT NULL default '0.00',
  `qty` int(10) unsigned NOT NULL default '0',
  `articleid` int(10) unsigned NOT NULL default '0',
  `variantid` int(10) unsigned NOT NULL default '0',
  `productid` varchar(255) NOT NULL default '',
  `subtitle` varchar(255) NOT NULL default '',
  `teaser` text NULL,
  `articleOptions` text NULL,
  `articleComment` text NULL,
    `options` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_taxzones` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `taxCountries` text NULL,
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_taxclasses` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_taxes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `tax_rate` float(9,2) unsigned NOT NULL default '0.00',
  `tax_class` int(10) unsigned NOT NULL default '0',
  `tax_zone` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_shippingoptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `shippingZones` text NULL,
  `shippingPriceType` varchar(255) NOT NULL default '',
  `shippingPricesWeight` text NULL,
  `shippingPricesPrice` text NULL,
  `shippingInfo` text NULL,
  `shippingTax` int(10) unsigned NOT NULL default '0',
  `published` char(1) NOT NULL default '',
  `guestAllowed` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_shippingzones` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `shippingCountries` text NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_paymentmodules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `paymentModule` varchar(255) NOT NULL default '',
  `paymentFee` float(9,2) unsigned NOT NULL default '0.00',
  `paymentConfig` text NULL,
  `discount` varchar(255) NOT NULL default '',
  `paymentTax` int(10) unsigned NOT NULL default '0',
  `published` char(1) NOT NULL default '',
  `groups` text NULL,
  `paymentText` text NULL,
  `paymentMail` text NULL,
  `guestAllowed` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_coupons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `amount` varchar(32) NOT NULL default '',
  `limitUse` char(1) NOT NULL default '',
  `maxUse` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `validUntil` varchar(10) NOT NULL default '',
  `couponTax` int(10) unsigned NOT NULL default '0',
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_module` (
  `webShop_startPoint` int(10) unsigned NOT NULL default '0',
  `webShop_articleListTemplate` varchar(255) NOT NULL default '',
  `webShop_navigationTemplate` varchar(255) NOT NULL default '',
  `webShop_articleListItemTemplate` varchar(255) NOT NULL default '',
  `webShop_articleTemplate` varchar(255) NOT NULL default '',
  `webShop_cartTemplate` varchar(255) NOT NULL default '',
  `webShop_thumbSize` varchar(255) NOT NULL default '',
  `webShop_fullSize` varchar(255) NOT NULL default '',
  `webShop_miniSize` varchar(255) NOT NULL default '',
  `wsGallery` varchar(255) NOT NULL default '',
  `webShop_jumpToAddressBook` int(10) unsigned NOT NULL default '0',
  `webShop_useParentCategoryName` char(1) NOT NULL default '',
  `webShop_jumpBack` int(10) unsigned NOT NULL default '0',
  `webShop_jumpLogin` int(10) unsigned NOT NULL default '0',
  `webShop_categoryImageSize` text NULL,
  `webShop_showWSPGroups` text NULL,
  `webShop_asImage` char(1) NOT NULL default '',
  `webShop_addFilter` char(1) NOT NULL default '',
  `webShop_addArticleList` char(1) NOT NULL default '',
  `webShop_cloudLimit` int(10) unsigned NOT NULL default '0',
  `webShop_cloudSizeMax` int(10) unsigned NOT NULL default '0',
  `webShop_cloudSizeMin` int(10) unsigned NOT NULL default '0',
  `webShop_limit` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_member` (
  `ustid` varchar(255) NOT NULL default '',
  `ustid_valid` char(1) NOT NULL default '',
  `defaultAddress` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `bankname` varchar(255) NOT NULL default '',
  `banknumber` varchar(255) NOT NULL default '',
  `bankaccount` varchar(255) NOT NULL default '',
  `iban` varchar(255) NOT NULL default '',
  `bic` varchar(255) NOT NULL default '',
  `bankowner` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_member_addressbook` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `postal` varchar(32) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `country` varchar(32) NOT NULL default '',
  `gender` varchar(6) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `company` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_productgroups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `addImage` char(1) NOT NULL default '',
  `singleSRC` varchar(255) NOT NULL default '',
  `keywords` text NULL,
  `description` text NULL,
  `pagetitle` varchar(255) NOT NULL default '',
  `description` text NULL,
  `descriptiontext` text NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_exports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `export_class` varchar(255) NOT NULL default '',
  `export_file` varchar(255) NOT NULL default '',
  `enabled` char(1) NOT NULL default ''
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_discount` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `usergroup` text NULL,
  `cartValue` float(9,2) unsigned NOT NULL default '0.00',
  `shippingFree` char(1) NOT NULL default '',
  `discountType` varchar(32) NOT NULL default '',
  `discountValue` float(9,2) unsigned NOT NULL default '0.00',
  `active` char(1) NOT NULL default '',
  `start` int(10) unsigned NOT NULL default '0',
  `stop` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tl_webshop_article_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `productgroup` int(10) unsigned NOT NULL default '0',
  `article` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

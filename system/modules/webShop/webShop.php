<?php

  class webShop extends Backend {
  
  	public function __construct() {
  		$this->Import('Database');
  		$this->Import('Config');
  		
  		return(parent::__construct());
  	}
  
  	public function updateAttributeDBAll() {

  		/* get all articles */
  		$res = $this->Database->prepare('SELECT * from tl_webshop_article')->execute();
  		while($res->next()) {
  			switch($res->type) {
		  			case 'article': {
					/* update product groups */
					$arrGroups = deserialize($res->productgroup);
			    	
			    	if(!is_array($arrGroups))
			    	  $arrGroups = array($arrGroups);
			    	/* delete old entries */
			    	$this->Database->prepare('DELETE FROM tl_webshop_article_groups where article=?')->execute($res->id);
			    	
			    	if(count($arrGroups)) {
			    		foreach($arrGroups as $grp) {
			    			if($grp < 1) continue;
			    			$arrAdd = array('productgroup' => $grp, 'article' => $res->id);
			    			$this->Database->prepare('INSERT INTO tl_webshop_article_groups %s')->set($arrAdd)->execute();
			    		}
			    	}
    
	  			} break;
  			}
  		}
  		
  		$this->redirect($this->getReferer());
  	}
  	
    
    
    public function updateProductGroups(DataContainer $dc) {
    	if(!$dc->activeRecord) return;
    	$arrGroups = deserialize($dc->activeRecord->productgroup);
    	
    	if(!is_array($arrGroups))
    	  $arrGroups = array($arrGroups);
    	/* delete old entries */
    	$this->Database->prepare('DELETE FROM tl_webshop_article_groups where article=?')->execute($dc->activeRecord->id);
    	
    	if(count($arrGroups)) {
    		foreach($arrGroups as $grp) {
    			$arrAdd = array('productgroup' => $grp, 'article' => $dc->activeRecord->id);
    			$this->Database->prepare('INSERT INTO tl_webshop_article_groups %s')->set($arrAdd)->execute();
    		}
    	}
    
    }
    
    
    
    public function generateExports() {
      $res = $this->Database->prepare('SELECT * from tl_webshop_exports where enabled=?')->execute(1);
      if($res->numRows > 0) {
        while($res->next()) {
          if(!file_exists(TL_ROOT .'/system/modules/webShop/exports/'. $res->export_class .'.php')) continue;
          require_once(TL_ROOT .'/system/modules/webShop/exports/'. $res->export_class .'.php');
          $objFile = new File('/system/modules/webShop/exports/'. $res->export_class .'.php');
          $filename = $objFile->filename;
          $objExport = new $filename();
          $objExport->saveFile = $res->export_file;
          $objExport->compile();
        }
      }
    }
    
		public function getSearchablePages($arrPages, $intRoot=0)
	  {
	    
	    $time = time();
	    // Get target pages
	    $res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCategory']);
      $arrJumpCategory = $res->fetchAssoc();
			$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToArticle']);
      $arrJumpArticle = $res->fetchAssoc();
			
			$domain = $this->Environment->base . TL_PATH;
			
			// Get all shop categories
	    $objCategory = $this->Database->execute("SELECT id, alias FROM tl_webshop_categories WHERE protected!=1 AND published=1");
			// return if there are no categories
			if($objCategory->numRows == 0) return($arrPages);
			
      while($objCategory->next()) {
      	$arrPages[] = $domain . $this->generateFrontendUrl($arrJumpCategory, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $objCategory->alias);
				$objArticle = $this->Database->prepare('SELECT * from tl_webshop_article where pid=? AND published=1')->execute($objCategory->id);
				if($objArticle->numRows == 0) continue;
				while($objArticle->next()) {
					$arrPages[] = $domain . $this->generateFrontendUrl($arrJumpArticle, '/'. $GLOBALS['webShop']['categoryKeyword'] .'/'. $objCategory->alias .'/'. $GLOBALS['webShop']['articleKeyword'] .'/'. $objArticle->alias);
				}
      }
			
      $this->generateExports();

	    return $arrPages;
	  }

		
  }

?>
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  class text extends Module {
    
    protected $moduleName = 'Textmeldung';
    
    protected $moduleConfigElements = array();
    protected $config = array();
    
    protected $data = array();
    
    public function __construct($arrConfig) {
      $this->Import('Database');
      $this->Import('Input');
      $this->Import('Config');
      // load language file
   		$lang = $GLOBALS['TL_LANGUAGE'];
  		if(!is_dir(TL_ROOT .'/system/modules/webShop/paymentModules/text/languages/'. $lang))
     		$lang = 'de';
      require_once(TL_ROOT .'/system/modules/webShop/paymentModules/text/languages/'. $lang .'/text.php');
			
      $this->moduleConfigElements = array(

      );
      
      if($this->Input->post('FORM_SUBMIT') == 'tl_webshop_paymentmodules') {
        $arrSubmit = $this->Input->post('paymentConfig');
        $config = serialize($arrSubmit);
        $res = $this->Database->prepare("UPDATE tl_webshop_paymentmodules set paymentConfig=? where id=?")->execute($config, $this->Input->get('id'));
      }
      $this->config = $arrConfig;
    }
        
    public function moduleInfo() {
      return($this->moduleName);
    }
        
    public function generateBEForm($arrConfig) {
      return('');
    }
    
    protected function compile() {
      return;
    }
    
    public function check() {
      return true;
    }
    
    public function getError() {
    
    }

    
  }

?>
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  class lastschrift extends Module {
    
    protected $moduleName = 'Lastschrift v0.1b';
    
    protected $moduleConfigElements = array();
    protected $config = array();
    
    public $data = array();
    
    public function __construct($arrConfig) {
      $this->Import('Database');
      $this->Import('Input');
      $this->Import('Config');
      // load language file
      $lang = $GLOBALS['TL_LANGUAGE'];
      if(!is_dir(TL_ROOT .'/system/modules/webShop/paymentModules/lastschrift/languages/'. $lang))
        $lang = 'de';
        
      require_once(TL_ROOT .'/system/modules/webShop/paymentModules/lastschrift/languages/'. $lang .'/lastschrift.php');
      
      $this->moduleConfigElements = array(
        'member_fields' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['lastschrift']['member_fields'],
          'inputType' => 'checkbox',
          'options' => $this->loadMemberfields(),
          'eval' => array('mandatory' => true, 'multiple' => true)
        )
      );
      
      if($this->Input->post('FORM_SUBMIT') == 'tl_webshop_paymentmodules') {
        $arrSubmit = $this->Input->post('paymentConfig');
        $config = serialize($arrSubmit);
        $res = $this->Database->prepare("UPDATE tl_webshop_paymentmodules set paymentConfig=? where id=?")->execute($config, $this->Input->get('id'));
      }
      $this->config = $arrConfig;
    }
    
    protected function loadMemberFields() {
      $arrRes = array();
      $this->loadDataContainer('tl_member');
      $this->loadLanguageFile('tl_member');
      foreach($GLOBALS['TL_DCA']['tl_member']['fields'] as $fld => $conf) {
        if($conf['eval']['feEditable'] == true)
          $arrRes[$fld] = $conf['label'][0];
      }
      return($arrRes);
    }
        
    public function moduleInfo() {
      return($this->moduleName);
    }
        
    public function generateBEForm($arrConfig) {
      $arrConfig = deserialize($arrConfig);
      foreach($this->moduleConfigElements as $name => $elem) {
        $cls = $GLOBALS['BE_FFL'][$elem['inputType']];
        $objElem = new $cls($this->prepareForWidget($elem, 'paymentConfig['. $name .']'));

        $html .= sprintf('<h3><label for="ctrl_%s">%s</label></h3>', 'paymentConfig['. $name .']', $elem['label'][0]);
        $objElem->value = $arrConfig[$name];
        $html .= $objElem->generate();
        $html .= sprintf('<p style="margin-bottom: 10px;" class="tl_help">%s</p>', $elem['label'][1]);
      }
      return($html);
    }
    
    protected function compile() {
      if($this->data['billingValue']) {
        return(str_replace("\n", "<br/>", $this->config['message']));
      }
    }
    
    public function check() {
    	$allOk = true;
      $this->Import('FrontendUser', 'User');
			$cfg = deserialize($this->config);
			foreach($cfg['member_fields'] as $fld) {
				if($this->User->$fld == '') {
				  $allOk = false;
				}
			}
			return($allOk);
    }
    
    public function getError() {
    	
    }

    
  }

?>
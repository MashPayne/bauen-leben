<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  class paypal extends Module {
  	
		protected $moduleName = 'Paypal Payment Module v0.3';
		
	  	protected $moduleConfigElements = array();
		protected $config = array();
		
		public $data = array();		
		
		public function __construct($arrConfig) {
			$this->Import('Database');
			$this->Import('Input');
			$this->Import('Config');
			$this->Import('Environment');
			
			// load language file
      		$lang = $GLOBALS['TL_LANGUAGE'];
      		if(!is_dir(TL_ROOT .'/system/modules/webShop/paymentModules/paypal/languages/'. $lang))
        		$lang = 'de';
        	require_once(TL_ROOT .'/system/modules/webShop/paymentModules/paypal/languages/'. $lang .'/paypal.php');
        	
	      $this->moduleConfigElements = array(
	      'business' => array(
	        'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['paypal']['business'],
	        'inputType' => 'text',
	        'eval' => array('rgxp' => 'email', 'mandatory' => true)
	      ),
	      'articleName' => array(
	        'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['paypal']['articleName'],
	        'inputType' => 'text',
	        'eval' => array('mandatory' => true)
	      ),
				'btnLabel' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['paypal']['btnLabel'],
					'inputType' => 'text'
				),
        'currency' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['paypal']['currency'],
          'inputType' => 'select',
          'options' => array(
            'EUR' => 'Euro',
            'GBP' => 'Pounds Sterling',
            'CHF' => 'Swiss Franc',
            'SEK' => 'Swedish Krona'
          ),
          'eval' => array('mandatory' => true)
        ),
        'enable_ipn' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['paypal']['enable_ipn'],
          'inputType' => 'checkbox'
        )
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
      $arrConfig = deserialize($arrConfig);
      foreach($this->moduleConfigElements as $name => $elem) {
      	if($GLOBALS['BE_FFL'][$elem['inputType']] != '') {
      		$frmWdg = $GLOBALS['BE_FFL'][$elem['inputType']];
					$objElem = new $frmWdg($this->prepareForWidget($elem, 'paymentConfig['. $name .']'));
	        $html .= sprintf('<h3><label for="ctrl_%s">%s</label></h3>', 'paymentConfig['. $name .']', $elem['label'][0]);
	        $objElem->value = $arrConfig[$name];
	        $html .= $objElem->generate();
	        $html .= sprintf('<p class="tl_help">%s</p>', $elem['label'][1]);
				}
      }
      return($html);
    }
		
		protected function compile() {
      if($this->data['billingValue']) {
      	return(sprintf('
				<div id="frmPaypal">
					<form target="_paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				    <input type="hidden" name="cmd" value="_xclick"/>
				    <input type="hidden" name="business" value="%s"/>
				    <input type="hidden" name="item_name" value="%s"/>
						<input type="hidden" name="amount" value="%s"/>
						<input type="hidden" name="currency_code" value="%s"/>
						<input type="hidden" name="notify_url" value="%s"/> 
						<input type="hidden" name="custom" value="%s"/>
				    <input type="submit" value="%s" />
					</form>
				</div>
				', $this->config['business'], $this->config['articleName'], $this->data['billingValue'], $this->config['currency'], ($this->config['enable_ipn'] ? $this->Environment->base .'system/modules/webShop/ModulePaypalIPN.php' : ''), $this->data['id'], $this->config['btnLabel']
				));
      }
		}
		    
    public function check() {
      return true;
    }
		
    public function getError() {
    
    }

		
  }

?>
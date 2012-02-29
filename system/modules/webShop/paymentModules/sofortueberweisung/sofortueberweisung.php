<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  class sofortueberweisung extends Module {
    
    protected $moduleName = 'Sofortueberweisung';
    
    protected $moduleConfigElements = array();
    protected $config = array();
    
    public $data = array();
    
    public function __construct($arrConfig) {
      $this->Import('Database');
      $this->Import('Input');
      $this->Import('Config');
      // load language file
      		$lang = $GLOBALS['TL_LANGUAGE'];
      		if(!is_dir(TL_ROOT .'/system/modules/webShop/paymentModules/sofortueberweisung/languages/'. $lang))
        		$lang = 'de';
      require_once(TL_ROOT .'/system/modules/webShop/paymentModules/sofortueberweisung/languages/'. $lang .'/sofortueberweisung.php');
      
      $this->moduleConfigElements = array(
			  'userid' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['sofortueberweisung']['userid'],
          'inputType' => 'text',
          'eval' => array('mandatory' => true)
				),
			  'projectid' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['sofortueberweisung']['projectid'],
					'inputType' => 'text',
					'eval' => array('mandatory' => true)
				),
				'projectpass' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['sofortueberweisung']['projectpass'],
					'inputType' => 'text',
					'eval' => array('mandatory' => true)
				),
				'paymentTitle' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['sofortueberweisung']['paymentTitle'],
          'inputType' => 'text',
					'eval' => array('mandatory' => true)
				),
				'btnLabel' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['sofortueberweisung']['btnLabel'],
          'inputType' => 'text',
					'eval' => array('mandatory' => true)
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
      	$frm = sprintf('
				<form method="post" target="_sofortueberweisung" action="https://www.sofortueberweisung.de/payment/start">
<input type="hidden" name="user_id" value="%s" />
<input type="hidden" name="project_id" value="%s" />
<input type="hidden" name="reason_1" value="%s" />
<input type="hidden" name="amount" value="%s" />
<input type="hidden" name="user_variable_0" value="%s" />
<input type="hidden" name="user_variable_1" value="%s" />
<input type="hidden" name="hash" value="%s" />
<input name="currency_id" type="hidden" value="EUR"/>
<input type="submit" value="%s" />
</form>
				',
				  $this->config['userid'],
					$this->config['projectid'],
					$this->preparePaymentTitle($this->config['paymentTitle']),
					$this->data['billingValue'],
					$this->Input->get('orderKey'),
					$this->Input->get('orderId'),
					$this->generatePaymentHash(),
					$this->config['btnLabel']
				);
        return($frm);
      }
    }
    
    public function check() {
      return true;
    }
    
    public function getError() {
    
    }

		
		protected function preparePaymentTitle($t) {
      $this->Import('FrontendUser', 'User');
			$ar1 = array('--ORDERID--', '--USERID--');
			$ar2 = array($this->Input->get('orderId'), $this->User->id);
			return(str_replace($ar1, $ar2, $t));
		}
		
		protected function generatePaymentHash() {
			$data = array(
				$this->config['userid'], // user_id
				$this->config['projectid'], // project_id
				'', // sender_holder
				'', // sender_account_number
				'', // sender_bank_code
				'', // sender_country_id
				$this->data['billingValue'], // amount
				'EUR', // currency_id
				$this->preparePaymentTitle($this->config['paymentTitle']),// reason_1
				'', // reason_2
				$this->Input->get('orderKey'), // user_variable_0
				$this->Input->get('orderId'), // user_variable_1
				'', // user_variable_2
				'', // user_variable_3
				'', // user_variable_4
				'', // user_variable_5
				$this->config['projectpass'] // project_password
			);
			return(sha1(implode('|', $data)));
		}
    
  }

?>
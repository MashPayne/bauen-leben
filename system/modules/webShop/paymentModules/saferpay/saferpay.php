<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');



  class saferpay extends Module {
    
    protected $moduleName = 'Saferpay v0.1';
    
    protected $moduleConfigElements = array();
    protected $config = array();
    
    protected $url_payinit = 'https://www.saferpay.com/hosting/CreatePayInit.asp';
    protected $url_payconfirm = 'https://www.saferpay.com/hosting/VerifyPayConfirm.asp';
    protected $url_paycomplet = 'https://www.saferpay.com/hosting/PayComplete.asp';
    
    public $data = array();
    
    public function __construct($arrConfig) {
      $this->Import('Database');
      $this->Import('Input');
      $this->Import('Config');
			$this->Import('Environment');
      // load language file
     		$lang = $GLOBALS['TL_LANGUAGE'];
     		if(!is_dir(TL_ROOT .'/system/modules/webShop/paymentModules/saferpay/languages/'. $lang))
       		$lang = 'de';
     @require_once(TL_ROOT .'/system/modules/webShop/paymentModules/saferpay/languages/'. $lang .'/saferpay.php');
      
      $this->moduleConfigElements = array(
        'transaction_description' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['transaction_description'],
          'inputType' => 'text',
          'eval' => array('mandatory' => true)
        ),
				'buttonLabel' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['buttonLabel'],
          'inputType' => 'text',
          'eval' => array('mandatory' => true)
				),
				'page_success' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['page_success'],
					'inputType' => 'select',
					'options' => $this->loadPageTreeForSelect(),
					'eval' => array('includeBlankOption' => true)
				),
				'page_fail' => array(
				  'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['page_fail'],
          'inputType' => 'select',
         'inputType' => 'pageTree',
         //'options' => $this->loadPageTreeForSelect(),
					'eval' => array('fieldType' => 'radio', 'mandatory' => true)
				),
        'accountid' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['accountid'],
          'inputType' => 'text',
          'eval' => array('mandatory' => true)
        ),
        'enter_cvc' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['enter_cvc'],
          'inputType' => 'checkbox'
        ),
        'enter_owner' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['enter_owner'],
          'inputType' => 'checkbox'
        ),
        'transaction_currency' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['transaction_currency'],
          'inputType' => 'select',
          'options' => array('EUR', 'USD', 'CHF', 'CZK', 'DKK', 'GBP', 'PLN', 'SEK'),
          'reference' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['currencies']
        ),
        'terminal_language' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['terminal_language'],
          'inputType' => 'select',
          'options' => array('de', 'da', 'cs', 'en', 'es', 'fr', 'hr', 'it', 'hu', 'nl', 'no', 'pl', 'pt', 'sk', 'sl', 'fi', 'sv', 'tr', 'el', 'jp'),
          'reference' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['terminal_languages'],
        ),
        'color_menu' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_menu'],
          'inputType' => 'text'
        ),
        'color_menufont' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_menufont'],
          'inputType' => 'text'
        ),
        'color_body' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_body'],
          'inputType' => 'text'
        ),
        'color_bodyfont' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_bodyfont'],
          'inputType' => 'text'
        ),
        'color_head' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_head'],
          'inputType' => 'text'
        ),
        'color_headfont' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_headfont'],
          'inputType' => 'text'
        ),
        'color_headline' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_headline'],
          'inputType' => 'text'
        ),
        'color_link' => array(
          'label' => &$GLOBALS['TL_LANG']['webShop']['paymentModules']['saferpay']['color_link'],
          'inputType' => 'text'
        )
        
      );
     $GLOBALS['TL_DCA']['webShop_paymentModules']['fields'] = $this->moduleConfigElements;
     
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
       $objElem = new $cls($this->prepareForWidget($elem, 'paymentConfig['. $name .']', $this->config[$name], $name, 'webShop_paymentModules'));
       $html .= sprintf('<h3><label for="ctrl_%s">%s</label></h3>', 'paymentConfig['. $name .']', $elem['label'][0]);
       if(is_array($arrConfig) && strlen($arrConfig[$name]))
         $objElem->value = $arrConfig[$name];
       $html .= $objElem->generate();
       $html .= sprintf('<p style="margin-bottom: 10px;" class="tl_help">%s</p>', $elem['label'][1]);
     }
     return($html);
    }
    
    protected function compile() {
      $GLOBALS['TL_JAVASCRIPT']['saferpay'] = 'https://www.saferpay.com/OpenSaferpayScript.js';
      $url = $this->saferpayInit();
      return(sprintf('<input type="button" value="%s" onClick="OpenSaferpayTerminal(\'%s\', this, \'BUTTON\');"/>', $this->config['buttonLabel'], $url));
    }
    
    protected function saferpayInit() {
      $arrURIKeys = array();
      $strURL = '';
      
      $objRequest = new Request();
      $arrURI = array(
        'AMOUNT' => ($this->data['billingValue'] * 100), 
        'CURRENCY' => $this->config['transaction_currency'],
        'DESCRIPTION' => $this->config['transaction_description'],
        'DELIVERY' => 'no',
        'CCCVC' => $this->config['enter_cvc'] == '1' ? 'yes' : 'no',
        'ACCOUNTID' => $this->config['accountid'],
        'FAILLINK' => $this->buildLink($this->config['transactionFailed']),
        'SUCCESSLINK' => $this->buildLink($this->config['transactionSuccess']),
      	'ORDERID' => $this->data['id']
      );
      
      foreach($arrURI as $key => $val)
        $arrURIKeys[] = $key .'='. rawurlencode($val);
      
      $strURL = implode('&', $arrURIKeys);
			// use curl insteat of TL Request Class

     $objCurl = curl_init($this->url_payinit .'?'. $strURL);
      curl_setopt($objCurl, CURLOPT_PORT, 443);
      curl_setopt($objCurl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($objCurl, CURLOPT_HEADER, 0);
      curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
      $strReturn = curl_exec($objCurl);
      curl_close($objCurl);
			
			return($strReturn);
			
      $objRequest->send($this->url_payinit .'?'. $strURL);
      if($objRequest->hasError()) {
        $objEmail = new Email();
        $objEmail->from = $GLOBALS['TL_CONFIG']['adminEmail'];
        $objEmail->subject = 'Saferpay Gateway Error';
        $objEmail->text = 'There was an error. Details see below'. "\n\n\n". $objRequest->error;
        $objEmail->sendto($GLOBALS['TL_CONFIG']['adminEmail']);
      } else {

       return($objRequest->response);
      }
    }
    
    public function check() {
      return true;
    }
    
    public function getError() {
    
    }
    

	
		protected function loadPageTreeForSelect($space = 0, $root = 0) {
			$arrPages = array();
			$res = $this->Database->prepare('SELECT * from tl_page where pid=?')->execute($root);
			if($res->numRows == 0) return false;
			while($res->next()) {
				$arrPages[$res->id] = str_repeat('&nbsp;&nbsp;', $space) . $res->title;
				$subPages = $this->loadPageTreeForSelect(($space + 2), $res->id);
				if(is_array($subPages))
  				$arrPages = array_merge($arrPages, $subPages);
			}
			return($arrPages);
		}
		
		protected function buildLink($pageId) {
			$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($pageId);
			if($res->numRows == 0) {
				return($this->buildLink($this->getRootIdFromUrl()));
			}
			return($this->Environment->base . $this->generateFrontendUrl($res->fetchAssoc()));
			
		}
    
  }

?>
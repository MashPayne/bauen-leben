<?php 

  require_once('../../initialize.php');
  
  class ModulePaypalIPN extends Controller {
  
    public function __construct() {
      $this->Import('Database');
      $this->Import('Config');
      $this->Import('Input');
    }
    
    public function run() {
      $strRequest = 'cmd=_notify-validate';
      foreach($_POST as $key => $value) {
        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);
        $strRequest .= sprintf('&%s=%s', $key, urlencode(stripslashes($value)));
      }
      
      $custom = $this->Input->post('custom');
      $total = $this->Input->post('mc_gross');
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
		  curl_setopt($ch, CURLOPT_POST ,1);
		  curl_setopt($ch, CURLOPT_POSTFIELDS , $strRequest);
 		  curl_setopt($ch, CURLOPT_HEADER ,0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
      curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		  $res = curl_exec($ch);
      curl_close($ch);
      
      if (strcmp ($res, "VERIFIED") == 0) {
        $res = $this->Database->prepare('SELECT * from tl_webshop_orders where id=?')->execute($custom);
        $arrOrder = $res->fetchAssoc();
        if($arrOrder['billingValue'] == $total)
          $this->Database->prepare('UPDATE tl_webshop_orders set payed=? WHERE id=?')->execute(1, $custom);
      } else {
         // invalid
      }

    }
  
  }
  
  $objPaypalIPN = new ModulePaypalIPN();
  $objPaypalIPN->run();

?>
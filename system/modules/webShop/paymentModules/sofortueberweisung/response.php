<?php

  require_once('../../../../initialize.php');
	
  class sofortueberweisung_response extends Frontend {
  	
		public function __construct() {
			$this->Import('Database');
			$this->Import('Input');
		}
		
		public function run() {
			$fp = fopen(TL_ROOT .'/so.txt', 'w');
			foreach($_REQUEST as $key => $val) {
				fwrite($fp, sprintf("%s -> %s\n", $key, $val));
			}
			fclose($fp);
		}
		
  }
	
	$objResponse = new sofortueberweisung_response();
	$objResponse->run();

?>
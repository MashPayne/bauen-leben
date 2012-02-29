<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

  /**
  * TYPOlight webCMS
  * Copyright (C) 2005 Leo Feyer
  *
  * This program is free software: you can redistribute it and/or
  * modify it under the terms of the GNU Lesser General Public
  * License as published by the Free Software Foundation, either
  * version 2.1 of the License, or (at your option) any later version.
  * 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * Lesser General Public License for more details.
  * 
  * You should have received a copy of the GNU Lesser General Public
  * License along with this program. If not, please visit the Free
  * Software Foundation website at http://www.gnu.org/licenses/.
  *
  * PHP version 5
  * @copyright  Stefan Gandlau 2009
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  * @license    EULA 
  * @filesource
  */
  
  
  /**
  * Class webShopAjaxController
  *
  * @copyright  Stefan Gandlau 2009
  * @author     Stefan Gandlau <stefan@gandlau.net>
  * @package    webShop
  */
  
  class webShopAjaxController extends Controller {
    
		protected $ajaxActions = array();
		
	    public function generate() {
	      
	    }
		
		/*
		 * @return formated xml-document
		 */
		
		public function buildAjaxXML() {
			$this->addAction('token', '', REQUEST_TOKEN);
			$dom = new DOMDocument('1.0', 'UTF-8');
			  $base = $dom->createElement('webShop');
				  $actions = $dom->createElement('actions');
					  foreach($this->ajaxActions as $ajaxAction) {
					  	$action = $dom->createElement('action');
							$action->setAttribute('target', $ajaxAction['target']);
							$action->setAttribute('method', $ajaxAction['method']);
							if(!is_array($ajaxAction['data']))
  								$action->appendChild($dom->createCDATASection($ajaxAction['data']));
							else {
								foreach($ajaxAction['data'] as $id => $dataElem) {
									$option = $dom->createElement('option');
									$option->setAttribute('id', $id);
									if(is_array($dataElem['attributes'])) {
									  foreach($dataElem['attributes'] as $key => $val)
										  $option->setAttribute($val, $val);
									} else {
										if($dataElem['attributes'] != '')
  									  $option->setAttribute($dataElem['attributes'], $dataElem['attributes']);
									}
									$option->appendChild($dom->createCDATASection($dataElem['data']));
									$action->appendChild($option);
								}
							}
							$actions->appendChild($action);
					  }
				$base->appendChild($actions);
			$dom->appendChild($base);
		  $dom->formatOutput = true;
			$dom->xmlStandalone = true;
			return($dom->saveXML());
		}
		
		protected function buildJSON() {
			$this->addAction('token', '', REQUEST_TOKEN);
			return(json_encode($this->ajaxActions));
		}
		
		public function send() {
			die($this->buildJSON());
//			die($this->buildAjaxXML());
		}
    
		public function addAction($method = false, $target = false, $data = false) {
			if(!$method) return;
			if(is_array($method)) {
				$data = $method['data'];
				$target = $method['target'];
				$method = $method['method'];
			}
			$this->ajaxActions[] = array('target' => $target, 'method' => $method, 'data' => $data);
		}
		
    
  }

?>
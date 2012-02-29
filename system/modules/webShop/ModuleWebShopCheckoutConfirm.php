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
 * Class ModuleWebShopCheckoutConfirm
 *
 * @copyright  Stefan Gandlau 2009
 * @author     Stefan Gandlau <stefan@gandlau.net>
 * @package    webShop
 */

	require_once('functions.php');

	class ModuleWebShopCheckoutConfirm extends Module {
	
		protected $strTemplate = 'webShop_checkoutConfirm';
	
		public function generate() {
			if(TL_MODE == 'BE') {
				$t = new BackendTemplate('be_wildcard');
				$t->wildcard = '### WEBSHOP CHECKOUT CONFIRM ###';
				return($t->parse());
			}
			
			if(FE_USER_LOGGED_IN)
				$this->Import('FrontendUser', 'User');
			
			$this->Import('webShopCouponController', 'Coupons');
			$this->Import('webShopShoppingCart', 'Cart');
			$this->Import('webShopTaxController', 'Tax');
			$this->Cart->taxes = $this->Tax->taxes;
			$this->Import('webShopAddressBook', 'Book');
			$this->Import('webShopShippingController', 'Shipping');
			$this->Import('webShopPaymentController', 'Payment');
			
			return(parent::generate());
		}
		
		protected function compile() {
			$GLOBALS['TL_CSS']['arc'] = 'system/modules/webShop/html/arc.css';
			$GLOBALS['TL_JAVASCRIPT']['arc'] = 'system/modules/webShop/html/arc.js';
				
				
			// Shopping Cart
			$cartItems = $this->Cart->getItems();
		
			if(count($this->Cart->warnings)) {
				$this->redirect($this->generateFrontendUrl($this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($GLOBALS['TL_CONFIG']['webShop_jumpToCart'])->fetchAssoc()));
			}
			
			$cartNetto = $this->Cart->netto;
			$cartBrutto = $this->Cart->brutto;
			$cartNettoX = $cartNetto;
			$cartBruttoX = $cartBrutto;
				
			// taxes array (filled from cart)
			$taxes = $this->Cart->taxes;
			
			// coupons
			$coupons = $this->Coupons->coupons;
			$saveNetto = 0;
			$saveBrutto = 0;
			foreach($coupons as $i => $coupon) {
				if(stristr($coupon['amount'], '%')) {
					// percent
					$coupons[$i]['perc'] = str_replace('%', '', $coupon['amount']);
					// value
					if($this->Tax->showBrutto) {
						$coupons[$i]['value'] = $cartBrutto * ($coupons[$i]['perc'] / 100);
					} else {
						$coupons[$i]['value'] = $cartNetto * ($coupons[$i]['perc'] / 100);
					}
				} else {
					if($this->Tax->showBrutto)
						$coupons[$i]['perc'] = $coupon['amount'] / $cartBrutto * 100;
					else
						$coupons[$i]['perc'] = $coupon['amount'] / $cartNetto * 100;
				}
			}
		
			// taxes
			$couponPerc = 0;
			foreach($coupons as $c) {
				$couponPerc += $c['perc'];
			}
		
			if($couponPerc > 0) {
				$cartBrutto = $cartBrutto - ($cartBrutto * $couponPerc / 100);
				$cartNetto = $cartNetto - ($cartNetto * $couponPerc / 100);
			}
			foreach($taxes as $i => $tax) {
				if($tax['sum'] > 0) {
					$taxes[$i]['sum'] = ($tax['sum'] - ($tax['sum'] * ($couponPerc / 100)));
				}
			}
			// Shipping options, prices taxes
			$this->Book->select = $_SESSION['webShop']['checkout']['shippingAddress'];
			$shippingAddress = $this->Book->shippingAddress;
				
			$this->Shipping->cartItems = $cartItems;
			$this->Shipping->select = $_SESSION['webShop']['checkout']['shippingMethod'];
			$this->Shipping->country = $shippingAddress['country'];
			$shipping = $this->Shipping->selectedOption;
			
			/* check discounts */
			if(FE_USER_LOGGED_IN) {
				$res = $this->Database->prepare('SELECT * from tl_webshop_discount where cartValue < ? AND active=?')->execute($GLOBALS['TL_CONFIG']['webShop_pricesBrutto'] ? $cartBrutto : $cartNetto, 1);
				if($res->numRows > 0) {
					$arrDiscounts = array();
					$_groups = $this->User->groups;
					while($res->next()) {
						if($res->start > 0 || $res->stop > 0) {
							if($res->start > 0) {
								$objStart = new Date($res->start);
								if($objStart->dayBegin > time()) continue;
							}
							if($res->stop > 0) {
								$objStop = new Date($res->stop);
								if($objStop->dayEnd < time()) continue;
							}
						}
						$arrGroups = deserialize($res->usergroup, true);
						if(array_intersect($arrGroups, $_groups)) {
							if($res->shippingFree == '1') {
								$shipping['shippingFee'] = 0;
								$shipping['tax'] = 0;
							}
		
							if($res->discountType == 'percent') {
								$arrDiscounts[] = array($res->title, $cartBrutto * ($res->discountValue / 100) * -1);
								$cartBrutto = $cartBrutto - ($cartBrutto * ($res->discountValue / 100));
								$cartNetto = $cartNetto - ($cartNetto * ($res->discountValue / 100));
								foreach($taxes as $id => $t) {
									$taxes[$id]['sum'] = $taxes[$id]['sum'] - ($taxes[$id]['sum'] * ($res->discountValue / 100));
								}
							} elseif($res->discountType == 'value') {
								$arrDiscounts[] = array($res->title, $res->discountValue * -1);
								if($this->Tax->showBrutto)
									$perc = $res->discountValue / $cartBrutto;
								else
									$perc = $res->discountValue / $cartNetto;

								$cartBrutto = $cartBrutto - ($cartBrutto * $perc);
								$cartNetto = $cartNetto - ($cartNetto * $perc);
								foreach($taxes as $id => $t) {
									$taxes[$id]['sum'] = $taxes[$id]['sum'] - ($taxes[$id]['sum'] * $perc);
								}
							}
						}
					}
					if(count($arrDiscounts) > 0)
						$this->Template->discounts = $arrDiscounts;
					}
				}
				if($shipping['shippingPriceType'] == 'shippingInfo') {
					$shipping['shippingFee'] = 0;
					$shipping['tax'] = 0;
				} else {
					if($GLOBALS['TL_CONFIG']['webShop_pricesBrutto']) {
						$shipping['netto'] = $shipping['shippingFee'] / ($taxes[$shipping['shippingTax']]['tax_rate'] / 100 + 1);
						$shipping['brutto'] = $shipping['shippingFee'];
						$shipping['tax'] = $shipping['brutto'] - $shipping['netto'];
					} else {
						$shipping['netto'] = $shipping['shippingFee'];
						$shipping['brutto'] = $shipping['shippingFee'] * ($taxes[$shipping['shippingTax']]['tax_rate'] / 100 + 1);
						$shipping['tax'] = $shipping['brutto'] - $shipping['netto'];
					}
		
					$taxes[$shipping['shippingTax']]['sum'] += $shipping['tax'];
				}
		
				// payment options
				$paymentAddress = $this->Book->getAddress(0);
				$this->Payment->select = $_SESSION['webShop']['checkout']['paymentMethod'];
				$this->Payment->totalPrice = array('netto' => $cartNettoX, 'brutto' => $cartBruttoX);
				$payment = $this->Payment->selectedOption;
			
				
				if($this->Tax->showBrutto) {
			
					$sum = $cartBrutto + ($shipping['netto'] + $shipping['tax']) + ($payment['netto'] + $payment['tax']);
					$taxExInc = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['inc'];
					$payment['paymentFee'] = $payment['tax'] + $payment['netto'];
					$taxes[$payment['paymentTax']]['sum'] += $payment['tax'];
					$paymentSum = $cartBrutto + $shipping['netto'] + $shipping['tax'] + $payment['netto'] + $payment['tax'];
					$shipping['shippingFee'] = $shipping['brutto'];
				} else {
					$sum = $cartNetto + $shipping['shippingFee'] + $payment['netto'];
					$payment['paymentFee'] = $payment['netto'];
					$taxExInc = $GLOBALS['TL_LANG']['webShop']['FE_LABEL']['ex'];
					if($this->Tax->calcBrutto) {
						$paymentSum = $cartBrutto + $shipping['netto'] + $shipping['tax'] + ($payment['netto'] + $payment['tax']);
						$taxes[$payment['paymentTax']]['sum'] += $payment['tax'];
					} else {
						$paymentSum = $cartNetto + $shipping['netto'] + $payment['netto'];
					}
				}
			
				/**
				 * fill the template
				 */
				$this->Template->items = $cartItems;
				// cartPrice
				if($this->Tax->showBrutto) {
					$this->Template->articleSum = $cartBruttoX;
				} else {
					$this->Template->articleSum = $cartNettoX;
				}
			
				
				// coupons and new cartPrice
					
				$this->Template->coupons = $coupons;
				if($this->Tax->showBrutto)
					$this->Template->articleSumCoupon = $cartBrutto;
				else
					$this->Template->articleSumCoupon = $cartNetto;
					
				$this->Template->shipping = $shipping;
				$this->Template->payment = $payment;
					
				$this->Template->paymentSum = $paymentSum;
				$this->Template->sum = $sum;
				$this->Template->taxExInc = $taxExInc;
					
				$this->Template->taxes = $taxes;
				$this->Template->calcBrutto = $this->Tax->calcBrutto;
				
				$this->Template->paymentAddress = $paymentAddress;
				$this->Template->shippingAddress = $shippingAddress;
				$this->Template->agbText = $GLOBALS['TL_CONFIG']['webShop_agb'] .'<br/><br/>';
				// check form fields
				$this->Template->agbChecked = strlen($this->Input->post('agb')) ? true : false;
				$this->Template->cancellationChecked = strlen($this->Input->post('cancellation')) ? true : false;
					
				$this->Template->customerMessage = $this->Input->post('customerMessage');
				
				// Links
				global $objPage;
				$this->Template->href = $this->generateFrontendUrl(array('id' => $objPage->id, 'alias' => $objPage->alias));
				
				$res = $this->Database->prepare('SELECT id, alias, title from tl_page where id=?')->execute($this->webShop_jumpBack);
				$arrBack = $res->fetchAssoc();
				
				$this->Template->lnkBack = array('title' => $arrBack['title'], 'href' => $this->generateFrontendUrl($arrBack));
				
				/**
				 * Save Order and Send Email
				 */
				
				
				if($this->Input->post('FORM_ACTION') == 'submitOrder') {
					$submit = true;
					if(!strlen($this->Input->post('agb'))) {
						$this->Template->agbwarn = true;
						$submit = false;
					}
				if(!strlen($this->Input->post('cancellation'))) {
					$this->Template->cancellationwarn = true;
					$submit = false;
				}
			
				if($submit) {
					// save to database
					$paymentAddress['phone'] = FE_USER_LOGGED_IN ? $this->User->phone : '';
					$arrSetOrder = array(
						'pid' => FE_USER_LOGGED_IN ? $this->User->id : 0,
						'tstamp' => time(),
						'datim' => time(),
						'shippingAddress' => serialize($shippingAddress),
						'paymentAddress' => serialize($paymentAddress),
						'shippingMethodData' => serialize($shipping),
						'paymentMethodData' => serialize($payment),
						'coupons' => serialize($this->Coupons->coupons),
						'billingValue' => $paymentSum,
						'taxes' => serialize($this->Cart->taxes),
						'orderStatus' => 'new',
						'ip' => $this->Environment->ip,
						'orderComment' => $this->Input->post('customerMessage'),
						'email' => FE_USER_LOGGED_IN ? $this->User->email : $paymentAddress['email']
					);
					$res = $this->Database->prepare('INSERT INTO tl_webshop_orders %s')->set($arrSetOrder)->execute();
					$orderId = $res->insertId;
					foreach($cartItems as $index => $item) {
						$cartItems[$index]->subtitle = '';
						$arrSet = array(
							'pid' => $orderId,
							'tstamp' => time(),
							'articleid' => $item->id,
							'variantid' => 0,
							'singlePrice' => $item->price,
							'qty' => $item->qty,
							'title' => $item->title,
							'productid' => $item->productid,
							'subtitle' => $item->subtitle,
							'teaser' => $item->teaser,
							'articleOptions' => serialize($item->specialdata),
							'articleComment' => strlen($item->comment) ? $item->comment : '',
							'options' => $item->optionList
						);
						$this->Database->prepare('INSERT INTO tl_webshop_orderitems %s')->set($arrSet)->execute();
	
						$this->Database->prepare('UPDATE tl_webshop_article set ordercount = ordercount + '. $item->qty .' where id=?')->execute($item->id);
					
						if($item->addStock == '1') {
							$this->Database->prepare('UPDATE tl_webshop_article set stock=stock - '. $item->qty .' where id=?')->execute($item->id);
						}
						
					}
					
					/* coupons */
					if(is_array($this->Coupons->coupons) && count($this->Coupons->coupons) > 0) {
						foreach($this->Coupons->coupons as $c) {
							if($c['limitUse'] == '1') {
								$this->Database->prepare('UPDATE tl_webshop_coupons set maxUse=maxUse-1 WHERE id=?')->execute($c['id']);
							}
						}
					}
					
					if(is_array($GLOBALS['TL_HOOKS']['webShopPostOrder']) && count($GLOBALS['TL_HOOKS']['webShopPostOrder']) > 0) {
						foreach($GLOBALS['TL_HOOKS']['webShopPostOrder'] as $callback) {
							$objCB = new $callback[0]();
							$objCB->$callback[1]($orderId);
							$arrEmlText = $objCB->email();
						}
					}
				
					// create email object
					$objEmail = new Email();
						
					// set sender
					$objEmail->from = $GLOBALS['TL_CONFIG']['webShop_email_mail'];
					$objEmail->fromName = $GLOBALS['TL_CONFIG']['webShop_email_name'];
						
					// set subject
					$objEmail->subject = $GLOBALS['TL_CONFIG']['webShop_email_subject'];
						
					// add file attachments
					$arrFiles = deserialize($GLOBALS['TL_CONFIG']['webShop_email_attachment']);
					if(count($arrFiles)) {
						foreach($arrFiles as $file) {
							if(file_exists(TL_ROOT .'/'. $file)) {
								$objEmail->attachFile($file);
							}
						}
					}
						
					//load Email Template
					$objT = new FrontendTemplate('webShop_orderMail');
					$objT->orderId = $orderId;
					$objT->items = $cartItems;
					if($this->Tax->showBrutto)
						$objT->articleSum = $this->Cart->brutto;
					else
						$objT->articleSum = $this->Cart->netto;
					
					$objT->shipping = $shipping;
					$objT->payment = $payment;
					
					$objT->paymentSum = $paymentSum;
					$objT->sum = $sum;
					$objT->taxExInc = $taxExInc;
					
					$objT->taxes = $taxes;
					$objT->calcBrutto = $this->Tax->calcBrutto;
					$objT->coupons = $coupons;
						
					$objT->paymentAddress = $paymentAddress;
					$objT->shippingAddress = $shippingAddress;
					$objCss = new FrontendTemplate('webShop_orderMailCss');
					$objT->css = $objCss->parse();
					$objT->customer = $this->Book->getAddress(0);
					$objT->paymentSum = $paymentSum;
					$objT->phone = $this->User->phone ? $this->User->phone : '';
					$objT->message = $this->Input->post('customerMessage');
					$objT->paymentMessage = $payment['paymentMail'];
					$objT->email_footer = $GLOBALS['TL_CONFIG']['webShop_email_footer'];
					$objEmail->html = $objT->parse();
					$arrSendTo = $this->getEmail(deserialize($GLOBALS['TL_CONFIG']['webShop_email_sendTo'], true));
					if(count($arrSendTo))
						$objEmail->sendBcc($arrSendTo);
					
					$objEmail->sendTo(FE_USER_LOGGED_IN ? $this->User->email : $paymentAddress['email']);
				
					if(is_array($GLOBALS['TL_HOOKS']['webShopPostOrder']) && count($GLOBALS['TL_HOOKS']['webShopPostOrder']) > 0) {
						foreach($GLOBALS['TL_HOOKS']['webShopPostOrder'] as $callback) {
							$objCB = new $callback[0]();
							$objCB->$callback[1]($orderId);
						}
					}
				
					unset($_SESSION['webShop']);
					$res = $this->Database->prepare('SELECT id, alias from tl_page where id=?')->execute($this->jumpTo);
					$arrJumpTo = $res->fetchAssoc();
					$this->redirect($this->generateFrontendUrl($arrJumpTo) .'?orderId='. $orderId .'&amp;orderKey='. md5($GLOBALS['TL_CONFIG']['encryptionKey'] . $arrSetOrder['datim']) );
				}
			}
		}
		
		
			
		protected function getEmail($arrId) {
			if(count($arrId) == 0) return(array());
			if($arrId[0] == '') return(array());
				
			$arrMail = array();
			$res = $this->Database->prepare('SELECT email from tl_user where id IN ('. implode(',', $arrId) .')')->execute();
			if($res->numRows > 0) {
				while($res->next())
					$arrMail[] = $res->email;
			}
			return($arrMail);
		}
	}

?>
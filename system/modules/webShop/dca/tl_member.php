<?php

  $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace('firstname,lastname', 'title,firstname,lastname', $GLOBALS['TL_DCA']['tl_member']['palettes']['default']);
  $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(';{address_legend:hide}', ',ustid,ustid_valid;{banking_legend:hide},bankname,bankowner,bankaccount,banknumber,iban,bic;{address_legend:hide}', $GLOBALS['TL_DCA']['tl_member']['palettes']['default']);
  
  $GLOBALS['TL_DCA']['tl_member']['fields']['ustid'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['ustid'],
		'inputType' => 'text',
		'eval' => array('maxlength' => 255, 'feGroup' => 'personal', 'feEditable' => true)
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['ustid_valid'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['ustid_valid'],
		'inputType' => 'checkbox',
	    'eval' => array('tl_class' => 'clr') 
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['defaultAddress'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['defaultAddress'],
		'inputType' => 'select',
		'options_callback' => array('tl_member_webShop', 'showAddressBook')
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['title'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['title'],
		'inputType' => 'text',
		'eval' => array('feGroup' => 'personal', 'feEditable' => true)
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['bankname'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['bankname'],
		'inputType' => 'text',
    'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['bankaccount'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['bankaccount'],
		'inputType' => 'text',
		'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	$GLOBALS['TL_DCA']['tl_member']['fields']['banknumber'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['banknumber'],
    'inputType' => 'text',
    'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	$GLOBALS['TL_DCA']['tl_member']['fields']['iban'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['iban'],
    'inputType' => 'text',
    'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	$GLOBALS['TL_DCA']['tl_member']['fields']['bic'] = array(
	  'label' => &$GLOBALS['TL_LANG']['tl_member']['bic'],
    'inputType' => 'text',
    'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	
	$GLOBALS['TL_DCA']['tl_member']['fields']['bankowner'] = array(
		'label' => &$GLOBALS['TL_LANG']['tl_member']['bankowner'],
    	'inputType' => 'text',
    	'eval' => array('feGroup' => 'bank', 'feEditable' => true, 'tl_class' => 'w50')
	);
	
	
  // change some fields
  $arrMemberMandatory = array(
	  'street', 'zip', 'city', 'country', 'postal', 'phone'
	);
	foreach($arrMemberMandatory as $fld)
    $GLOBALS['TL_DCA']['tl_member']['fields'][$fld]['eval']['mandatory'] = true;
	

  class tl_member_webShop extends Backend {
  	
		public function showAddressBook($objDc) {
			$arrRes = array();
			$arrCountries = $this->getCountries();
			$arrRes[''] = $GLOBALS['TL_LANG']['tl_member']['accountAddress'];
			$res = $this->Database->prepare('SELECT * from tl_member_addressbook where pid=?')->execute($objDc->id);
			if($res->numRows == 0) return($arrRes);
			while($res->next())
			  $arrRes[$res->id] = sprintf('%s %s, %s, %s, %s (%s)', $res->firstname, $res->lastname, $res->street, $res->postal, $res->city, $arrCountries[$res->country]);	
			return($arrRes);
			
		}
		
  }
	
?>
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

class groupPriceWidget extends Widget {
	protected $blnSubmitInput = true;
	protected $strTemplate = 'groupPrices_widget';
  
	public function __construct($arrAttributes=false)	{
		parent::__construct($arrAttributes);
		$this->decodeEntities = true;
	}

	public function __set($strKey, $varValue) {
		switch ($strKey) {
			case 'groupPrices': {
				$this->varValue = deserialize($varValue);
        }
				break;
			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}

	
	protected function validator($varInput)	{
		if (is_array($varInput)) {
			return parent::validator($varInput);
		}
		return parent::validator(trim($varInput));
	}

	public function generate() {

	}

	public function generateFormFields() {
	  $this->Import("Database", "Database");
    $res = $this->Database->prepare("SELECT * from tl_member_group order by name")->execute();
    while($res->next()) {
      $arrGroups[$res->id] = array("name" => $res->name, "id" => $res->id);
    }
    $arrFields = array();

    $groupbox = "<option value=\"-1\">-</option>";
    foreach($arrGroups as $g)
      $groupbox .= "<option value=\"". $g["id"] ."\">". $g["name"] ."</option>";
    
    

    if(is_array($this->varValue)) {
      $arrG = $this->varValue["group"];
      $arrV = $this->varValue["value"];
      foreach($arrG as $index => $group) {
        if($arrV[$index] == "") continue;
        $groupbox = "<option value=\"-1\">-</option>";
        foreach($arrGroups as $g) {
          if($g["id"] == $group)
            $add = " selected=\"selected\"";
          else
            $add = "";
          $groupbox .= "<option value=\"". $g["id"] ."\"$add>". $g["name"] ."</option>";
        }
        $arrFields[] = sprintf('<tr><td><select name="%s[group][]">%s</select></td>
          <td><input type="text" name="%s[value][]" id="ctrl_%s" class="tl_text_%s" value="%s"%s onfocus="Backend.getScrollOffset();" /></td></tr>',
                  
									$this->strName,
                  $groupbox,
                  $this->strName,
									$this->strId.'_'.$i,
									$this->size,
									specialchars($arrV[$index]),
									$this->getAttributes());
      }
    }

    $groupbox = "<option value=\"-1\">-</option>";
    foreach($arrGroups as $g)
      $groupbox .= "<option value=\"". $g["id"] ."\">". $g["name"] ."</option>";
    
		$arrFields[] = sprintf('<tr><td><select name="%s[group][]">%s</select></td>
    <td><input type="text" name="%s[value][]" id="ctrl_%s" class="tl_text_%s" value=""%s onfocus="Backend.getScrollOffset();" /></td></tr>',
                  
									$this->strName,
                  $groupbox,
                  $this->strName,
									$this->strId.'_'.$i,
									$this->size,
									$this->getAttributes());

		return sprintf('%s', implode(' ', $arrFields));
	}
}

?>
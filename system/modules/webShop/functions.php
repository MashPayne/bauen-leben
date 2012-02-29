<?php

  /**
   * Helper functions
   */

  function formatPrice($value, $addSign = false) {
  	return(number_format($value, $GLOBALS['TL_CONFIG']['webShop_currencyDecimals'], $GLOBALS['TL_CONFIG']['webShop_currencyDecimal'], $GLOBALS['TL_CONFIG']['webShop_currencyThausands']) . ($addSign == true ? ' '. $GLOBALS['TL_CONFIG']['webShop_currencySign'] : ''));
  }

?>
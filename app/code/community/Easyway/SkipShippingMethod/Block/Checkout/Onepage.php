<?php
class Easyway_SkipShippingMethod_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
  public function getSteps()
  {
    $steps = parent::getSteps();
    if ( Mage::helper('skipshippingmethod')->isSkipEnabled() ) {
      if ( isset($steps['login']) ) { unset($steps['login']); }
    }
    if ( Mage::helper('skipshippingmethod')->isEnabled() ) {
      if ( isset($steps['shipping_method']) ) { unset($steps['shipping_method']); }
    }
    return $steps;
  }
}

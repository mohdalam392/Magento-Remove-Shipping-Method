<?php
class Easyway_SkipShippingMethod_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function isEnabled()
  {
    return Mage::getStoreConfigFlag('checkout/skipshippingmethod/enabled');
  }
  public function isSkipEnabled()
    {
        return Mage::getStoreConfig('checkout/skipstep1/enabled');
    }
    public function isLoginlinkEnabled()
    {
        return Mage::getStoreConfig('checkout/skipstep1/login_link');
    }
}

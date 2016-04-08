<?php
class Easyway_SkipShippingMethod_Model_Adminhtml_System_Method
{
  const FIRST_AVAILABLE = 'first_available';
  const LOWEST_COST     = 'lowest_cost';
  const HIGHEST_COST    = 'highest_cost';

  public function toOptionArray()
  {
    return array(
      array(
      'value' => self::FIRST_AVAILABLE,
      'label' => Mage::helper('skipshippingmethod')->__('First Available Shipping Method'),
      ),
      array(
      'value' => self::LOWEST_COST,
      'label' => Mage::helper('skipshippingmethod')->__('Lowest Shipping Cost'),
      ),
      array(
      'value' => self::HIGHEST_COST,
      'label' => Mage::helper('skipshippingmethod')->__('Highest Shipping Cost'),
      ));
  }

}

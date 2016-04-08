<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Easyway_SkipShippingMethod_Checkout_OnepageController extends Mage_Checkout_OnepageController
{

  public function saveBillingAction()
  {
    parent::saveBillingAction();
    if ($this->getRequest()->isPost()) { $this->checkAndSetShippingMethod(); }
  }

  public function saveShippingAction()
  {
    parent::saveShippingAction();
    if ($this->getRequest()->isPost()) { $this->checkAndSetShippingMethod(); }
  }

  protected function checkAndSetShippingMethod()
  {
    if ( Mage::helper('skipshippingmethod')->isEnabled() ) {
      $result = Mage::helper('core')->jsonDecode($this->getResponse()->getBody());
      if (!isset($result['error']) && isset($result['goto_section']) && $result['goto_section'] == 'shipping_method') {
        $code = $this->_getShippingCode();

        if ( is_null($code) ) {
          $result = array(
            'error' => 1,
            'message' => array(Mage::getStoreConfig('checkout/skipshippingmethod/error'))
          );
        } else {
          $result = $this->getOnepage()->saveShippingMethod($code);
          if( !$result ) { 
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
            $this->getOnepage()->getQuote()->getShippingAddress()->collectTotals()->save();
            $result['goto_section'] = 'payment';
            $result['allow_sections'] = array('shipping');
            $result['update_section'] = array(
              'name' => 'payment-method',
              'html' => $this->_getPaymentMethodsHtml()
              );
          }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
      }
    }
  }

  protected function _getPaymentMethodsHtml()
  {
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->setCacheId('LAYOUT_'.Mage::app()->getStore()->getId().md5('checkout_onepage_paymentmethod'));
    $update->load('checkout_onepage_paymentmethod');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();
    return $output;
  }

  protected function _getShippingCode()
  {
    switch ( Mage::getStoreConfig('checkout/skipshippingmethod/method') ) {
      case Hetinfoway_SkipShippingMethod_Model_Adminhtml_System_Method::FIRST_AVAILABLE:
        $code = $this->_getFirstShippingMethod();
        break;
      case Hetinfoway_SkipShippingMethod_Model_Adminhtml_System_Method::LOWEST_COST:
        $code = $this->_getLowestShippingRate();
        break;
      case Hetinfoway_SkipShippingMethod_Model_Adminhtml_System_Method::HIGHEST_COST:
        $code = $this->_getHighestShippingRate();
        break;
    }
    return $code;

  }

  protected function _getFirstShippingMethod()
  {
    $code = null;
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        $code = $rate->getCode();break;
      }
      break;
    }
    return $code;
  }

  protected function _getLowestShippingRate()
  {
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    $method = array('code' => null, 'value' => -1);
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        if ( is_null($method['code']) || $rate->getPrice() < $method['price'] ) {
          $method['code'] = $rate->getCode();
          $method['price'] = $rate->getPrice();
        }
        if ( $method['price'] == 0 ) { break; }
      }
      if ( $method['price'] == 0 ) { break; }
    }
    return $method['code'];
  }

  protected function _getHighestShippingRate()
  {
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    $method = array('code' => null, 'value' => -1);
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        if ( is_null($method['code']) || $rate->getPrice() > $method['price'] ) {
          $method['code'] = $rate->getCode();
          $method['price'] = $rate->getPrice();
        }
      }
    }
    return $method['code'];
  }

}

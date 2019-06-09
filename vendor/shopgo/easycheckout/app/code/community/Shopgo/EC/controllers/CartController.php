<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Shopgo_EC_CartController extends Mage_Checkout_CartController {
    /**
     * Update customer's shopping cart
     */
    protected function _updateShoppingCart() {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                /*
                 * Zend_Filter_LocalizedToNormalized filter will break cart qty update in Arabic.
                 */
                /*
                  $filter = new Zend_Filter_LocalizedToNormalized(
                  array('locale' => Mage::app()->getLocale()->getLocaleCode())
                  );
                  foreach ($cartData as $index => $data) {
                  if (isset($data['qty'])) {
                  $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                  }
                  }
                 */
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                        ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }
}
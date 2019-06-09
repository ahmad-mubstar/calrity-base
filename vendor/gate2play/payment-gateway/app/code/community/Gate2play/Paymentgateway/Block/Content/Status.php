<?php
/**
 * Gate2play Payment payment method model.
 *
 * @category   Gate2play
 * @package    Gate2play_Paymentgateway
 * @author     gate2play.com
 */
class Gate2play_Paymentgateway_Block_Content_Status extends Mage_Core_Block_Template
{
    
    /**
     * Return checkout session instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getImageSrc($image)
    {   
        $imageFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'gate2play' . DS . $image, array('_type' => 'skin'));

        if (file_exists($imageFilename)) {
            return $this->getSkinUrl('images/gate2play/' . $image);
        }

        return false;
    } 
  
}

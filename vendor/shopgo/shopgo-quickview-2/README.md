###Installation Guide###
####Dependencies:
*  jQuery (1.7 ~ 1.9)
* varien/product.js and calendar/calendar.js (commented in quickview.xml), if ajax cart is not installed.

Add Quickview link in product list view :

```
#!php
<?php if(class_exists('Shopgo_Quickview_Helper_Data') && Mage::getStoreConfigFlag('quickview/general/enable') == 1){
    ?>
    <div class="quickview-wrap">
        <a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" style="display:none"></a>
    </div>
<?php
}?>

```
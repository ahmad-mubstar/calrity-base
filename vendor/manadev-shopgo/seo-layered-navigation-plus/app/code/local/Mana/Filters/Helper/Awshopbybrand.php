<?php

class Mana_Filters_Helper_Awshopbybrand extends Mage_Core_Helper_Abstract
{
    /**
     * AW Shop By Brand module code
     */
    const MODULE_CODE = 'aw_shopbybrand_brand';

    /**
     * Check whether AW Shop By Brand module is installed
     *
     * @return bool
     */
    public function isAwShopbybrandInstalled()
    {
        return Mage::getConfig()->getModuleConfig('AW_Shopbybrand')->is('active', 'true');
    }

    /**
     * Handle AW Shop By Brand route path key value
     *
     * @param array $query
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function handleRoutePathKey($query, $key, $value)
    {
        if ($this->isAwShopbybrandInstalled()) {
            if (isset($query[self::MODULE_CODE])) {
                $value = self::MODULE_CODE;
            } elseif ($key == self::MODULE_CODE) {
                $value = false;
            }
        }

        return $value;
    }
}

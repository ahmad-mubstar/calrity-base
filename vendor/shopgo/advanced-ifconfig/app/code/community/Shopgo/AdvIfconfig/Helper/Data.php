<?php
/**
 * ShopGo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Shopgo
 * @package     Shopgo_AdvIfconfig
 * @copyright   Copyright (c) 2014 Shopgo. (http://www.shopgo.me)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Data helper
 *
 * @category    Shopgo
 * @package     Shopgo_AdvIfconfig
 * @author      Ammar <ammar@shopgo.me>
 */
class Shopgo_AdvIfconfig_Helper_Data extends Shopgo_Core_Helper_Abstract
{
    /**
     * Check system config node depends
     *
     * @param string $section
     * @param string $group
     * @param string $field
     * @param bool $result
     * @return bool
     */
    public function checkSystemConfigNodeDepends($section, $group, $field, $result = false)
    {
        $depends = $this->getSystemConfigNodeDepends($section, $group, $field);

        foreach ((array)$depends as $fieldName => $fieldValue) {
            $path = $section . '/' . $group . '/' . $fieldName;
            $dependValid = $fieldValue == Mage::getStoreConfigFlag($path);
            $result = $result && $this->checkSystemConfigNodeDepends(
                $section, $group, $fieldName, $dependValid
            );
        }

        return $result;
    }

    /**
     * Get system config node depends
     *
     * @param string $sectionName
     * @param string $groupName
     * @param string $fieldName
     * @return Varien_Simplexml_Element|null
     */
    public function getSystemConfigNodeDepends($sectionName, $groupName = null, $fieldName = null)
    {
        $config = Mage::getSingleton('adminhtml/config');
        $sectionName = trim($sectionName, '/');
        $path = '//sections/' . $sectionName;
        $groupNode = $fieldNode = null;
        $sectionNode = $config->getSections()->xpath($path);
        if (!empty($groupName)) {
            $groupPath = $path .= '/groups/' . trim($groupName, '/');
            $groupNode = $config->getSections()->xpath($path);
        }
        if (!empty($fieldName)) {
            if (!empty($groupName)) {
                $path .= '/fields/' . trim($fieldName, '/');
                $fieldNode = $config->getSections()->xpath($path);
            }
            else {
                Mage::throwException(
                    $this->__('The group node name must be specified with field node name.')
                );
            }
        }
        $path .= '/depends';
        $dependsNode = $config->getSections()->xpath($path);
        foreach ($dependsNode as $node) {
            return $node;
        }
        return null;
    }

    /**
     * Get store config with depends flag
     *
     * @param string $configPath
     * @param array $requiredDepends
     * @param string $type
     * @return bool
     */
    public function getStoreConfigWithDependsFlag($configPath, $requiredDepends = array(), $type = 'tree')
    {
        $ifConfig = Mage::getStoreConfigFlag($configPath);

        if ($ifConfig) {
            if ($type == 1 || $type == 'tree') {
                $configPath = explode('/', $configPath);
                $ifConfig = $ifConfig
                    && $this->checkSystemConfigNodeDepends(
                    $configPath[0], // Section
                    $configPath[1], // Group
                    $configPath[2], // Field
                    $ifConfig
                );
            }

            if ($type == 1 || $type == 'required') {
                if (is_string($requiredDepends)) {
                    $requiredDepends = array_map('trim', explode(',', $requiredDepends));
                }

                foreach ($requiredDepends as $depend) {
                    $ifConfig = $ifConfig && Mage::getStoreConfigFlag($depend);
                }
            }
        }

        return $ifConfig;
    }
}

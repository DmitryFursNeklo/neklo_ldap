<?php

class Neklo_LDAP_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @param Mage_Core_Model_Store|int|null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        $isEnabled = Mage::helper('neklo_ldap/config')->isEnabled($store);
        $isModuleEnabled = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();
        return $isEnabled && $isModuleEnabled && $isModuleOutputEnabled;
    }

}
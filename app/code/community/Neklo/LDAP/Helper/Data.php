<?php

class Neklo_LDAP_Helper_Data extends Mage_Core_Helper_Data
{
    const LDAP_EXTENSION_NAME = 'ldap';

    /**
     * @param Mage_Core_Model_Store|int|null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        $isLdapExtensionLoaded = $this->isLdapExtensionLoaded();
        $isEnabled = Mage::helper('neklo_ldap/config')->isEnabled($store);
        $isModuleEnabled = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();
        return $isLdapExtensionLoaded && $isEnabled && $isModuleEnabled && $isModuleOutputEnabled;
    }

    /**
     * @return bool
     */
    public function isLdapExtensionLoaded()
    {
        return extension_loaded(self::LDAP_EXTENSION_NAME);
    }
}
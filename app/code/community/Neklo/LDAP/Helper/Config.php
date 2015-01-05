<?php

class Neklo_LDAP_Helper_Config extends Mage_Compiler_Helper_Data
{
    const GENERAL_IS_ENABLED = 'neklo_ldap/general/is_enabled';

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::GENERAL_IS_ENABLED, $store);
    }

    const CONNECTION_HOST = 'neklo_ldap/connection/host';
    const CONNECTION_PORT = 'neklo_ldap/connection/port';
    const CONNECTION_VERSION = 'neklo_ldap/connection/version';
    const CONNECTION_TLS = 'neklo_ldap/connection/tls';
    const CONNECTION_ROOT_DN = 'neklo_ldap/connection/rootdn';
    const CONNECTION_BASE_DN = 'neklo_ldap/connection/basedn';
    const CONNECTION_ROOT_PASSWORD = 'neklo_ldap/connection/rootpassword';
    const CONNECTION_FILTER = 'neklo_ldap/connection/filter';
    const CONNECTION_ATTRIBUTE_LIST = 'neklo_ldap/connection/attribute_list';

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getHost($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_HOST, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getPort($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_PORT, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getVersion($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_VERSION, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return bool
     */
    public function canUseTls($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONNECTION_TLS, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getRootDn($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_ROOT_DN, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getBaseDn($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_BASE_DN, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getRootPassword($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_ROOT_PASSWORD, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return string
     */
    public function getFilter($store = null)
    {
        return Mage::getStoreConfig(self::CONNECTION_FILTER, $store);
    }

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return array
     */
    public function getAttributeList($store = null)
    {
        $attributeListJson = Mage::getStoreConfig(self::CONNECTION_ATTRIBUTE_LIST, $store);
        try {
            $attributeList = Mage::helper('neklo_ldap')->jsonDecode($attributeListJson);
        } catch (Exception $e) {
            $attributeList = array();
        }
        return $attributeList;
    }

    const ADMIN_ROLE_ID = 'neklo_ldap/admin/role_id';

    /**
     * @param Mage_Core_Model_Store|int|null $store
     *
     * @return int
     */
    public function getAdminRoleId($store = null)
    {
        return Mage::getStoreConfig(self::ADMIN_ROLE_ID, $store);
    }

    /**
     * @return string
     */
    public function getFullHost()
    {
        return $this->getProtocol() . $this->getHost() . '/';
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        if ($this->canUseTls()) {
            return 'ldaps://';
        }
        return 'ldap://';
    }
}
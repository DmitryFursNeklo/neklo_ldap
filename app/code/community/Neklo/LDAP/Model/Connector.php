<?php

class Neklo_LDAP_Model_Connector extends Varien_Object
{
    protected $_connection = null;

    protected function _construct()
    {
        $this->init();
    }

    public function init()
    {
        // connect to ldap
        if (is_null($this->getConnection())) {
            $this->_connection = ldap_connect($this->getConfig()->getFullHost(), $this->getConfig()->getPort());
        }

        // set protocol version
        if (!ldap_set_option($this->getConnection(), LDAP_OPT_PROTOCOL_VERSION, $this->getConfig()->getVersion())) {
            Mage::throwException('AUTH_ADMIN ERROR : VERSION ERROR');
        }

        // login with root dn
        if (!ldap_bind($this->getConnection(), $this->getConfig()->getRootDn(), $this->getConfig()->getRootPassword())) {
            Mage::throwException('AUTH_ADMIN ERROR : BIND ERROR');
        }
    }

    public function getLdapUser($adminName)
    {
        $sr = ldap_search($this->getConnection(), $this->getConfig()->getBaseDn(), $this->getConfig()->getFilter());
        if (!ldap_count_entries($this->getConnection(), $sr)) {
            Mage::throwException('AUTH_ADMIN ERROR : FILTER RESULT ERROR');
        }
        $entryList = ldap_get_entries($this->getConnection(), $sr);

        $ldapUser = null;
        for ($i = 0; $i < $entryList["count"]; $i++) {
            if ($entryList[$i]["uid"][0] !== $adminName) {
                continue;
            }
            // TODO: read attributes by config
            $ldapUser = new Varien_Object(
                array(
                    'dn'        => $entryList[$i]["dn"],
                    'username'  => $entryList[$i]["uid"][0],
                    'firstname' => $entryList[$i]["givenname"][0],
                    'lastname'  => $entryList[$i]["sn"][0],
                    'email'     => $entryList[$i]["mail"][0],
                    'password'  => $entryList[$i]["userpassword"][0],
                )
            );
        }
        return $ldapUser;
    }

    public function validateLdapUser($dn, $password)
    {
        // connect to ldap
        $connection = ldap_connect($this->getConfig()->getFullHost(), $this->getConfig()->getPort());

        // set protocol version
        if (!ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, $this->getConfig()->getVersion())) {
            Mage::throwException('AUTH_ADMIN ERROR : VERSION ERROR');
        }

        // login with user dn
        if (!ldap_bind($connection, $dn, $password)) {
            Mage::throwException('AUTH_ADMIN ERROR : VALIDATE USER ERROR');
        }

        ldap_close($connection);
    }

    /**
     * @return resource||null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return Neklo_LDAP_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('neklo_ldap/config');
    }

    public function close()
    {
        ldap_close($this->getConnection());
    }
}
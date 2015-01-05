<?php

class Neklo_LDAP_Model_System_Config_Source_Role
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getResourceModel('admin/roles_collection')->toOptionArray();
    }
}
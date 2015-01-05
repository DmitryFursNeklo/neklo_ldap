<?php

class Neklo_LDAP_Model_Observer
{
    public function actionPreDispatchAdmin($observer)
    {
        $this->_processAdminAction();
        // Always run native event
        Mage::getModel('admin/observer')->actionPreDispatchAdmin($observer);
    }

    protected function _processAdminAction()
    {
        if (!Mage::helper('neklo_ldap')->isEnabled()) {
            return;
        }

        /* @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        $request = Mage::app()->getRequest();
        $user = $session->getUser();

        if (!$this->_isAllowedAction($request->getActionName())) {
            return;
        }
        if ($user) {
            $user->reload();
        }

        if ($user && $user->getId()) {
            return;
        }
        if ($request->getPost('login', null) == null) {
            return;
        }

        $postLogin = $request->getPost('login');
        $username = isset($postLogin['username']) ? $postLogin['username'] : '';
        $password = isset($postLogin['password']) ? $postLogin['password'] : '';

        if (!$username || !$password) {
            return;
        }
        try {
            $handle = Mage::getModel('neklo_ldap/connector');
            $ldapUser = $handle->getLdapUser($username);
            if ($ldapUser === null) {
                return;
            }

            $handle->validateLdapUser($ldapUser->getDn(), $password);

            $user = $this->_updateUser($ldapUser);

            if (!$user->getIsActive()) {
                return;
            }

            $user->login($username, $ldapUser->getPassword());
            $session->renewSession();
            if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                Mage::getSingleton('adminhtml/url')->renewSecretUrls();
            }
            $session->setIsFirstPageAfterLogin(true);
            $session->setUser($user);
            $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

            Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
            $redirectUrl = Mage::getSingleton('adminhtml/url')->getUrl('*/*/*', array('_current' => true));

            Mage::app()->getResponse()
                ->clearHeaders()
                ->setRedirect($redirectUrl)
                ->sendHeadersAndExit();

            $request->setPost('login', null);
        } catch (Exception $e) {

        }
    }

    protected function _updateUser($ldapUser)
    {
        $adminUser = Mage::getModel('admin/user')->load($ldapUser->getUsername(), 'username');
        if (!$adminUser->getId()) {
            // create
            // TODO: read attributes by config
            $adminUser = Mage::getModel('admin/user')
                ->setData(
                    array(
                        'username'  => $ldapUser->getUsername(),
                        'firstname' => $ldapUser->getFirstname(),
                        'lastname'  => $ldapUser->getLastname(),
                        'email'     => $ldapUser->getEmail(),
                        'password'  => $ldapUser->getPassword(),
                        'is_active' => 1,
                    )
                )
                ->save()
            ;
            if (Mage::helper('neklo_ldap/config')->getAdminRoleId()) {
                $adminUser
                    ->setRoleIds(array(Mage::helper('neklo_ldap/config')->getAdminRoleId()))
                    ->setRoleUserId($adminUser->getUserId())
                    ->saveRelations()
                ;
            }
        } else {
            // update user
            // TODO: read attributes by config
            $adminUser
                ->setUsername($ldapUser->getUsername())
                ->setFirstname($ldapUser->getFirstname())
                ->setLastname($ldapUser->getLastname())
                ->setEmail($ldapUser->getEmail())
                ->setPassword($ldapUser->getPassword())
                ->save()
            ;
        }
        return $adminUser;
    }

    protected function _isAllowedAction($actionName)
    {
        $openActions = array(
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'logout',
            'refresh',
        );
        return !in_array($actionName, $openActions);
    }
}
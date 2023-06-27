<?php
/**
 * Magento Adminhtml Controller tests case
 *
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_Adminhtml_ControllerTestCase
 *
 * @package    Mage_Adminhtml
 * @subpackage Mage_Adminhtml_Test
 */
class Mage_Adminhtml_ControllerTestCase extends MageTest_PHPUnit_Framework_ControllerTestCase {

    /**
     * Fixture user name
     *
     * @var string
     **/
    protected static $userName;

    /**
     * Fixture first name
     *
     * @var string
     **/
    protected static $firstName;

    /**
     * Fixture lastName
     *
     * @var string
     **/
    protected static $lastName;

    /**
     * Fixture email
     *
     * @var string
     **/
    protected static $email;

    /**
     * Fixture password
     *
     * @var string
     **/
    protected static $password;

    /**
     * Fixture role name
     *
     * @var string
     **/
    protected static $roleName;

    /**
     * Set up the fixtures for the Adminhtml module tests
     *
     * @return void
     * @throws Throwable
     * @author Alistair Stead
     */
    public function setup(): void
    {
        parent::setup();
        self::$userName = 'fixture';
        self::$firstName = 'Test';
        self::$lastName = 'User';
        self::$email = 'test.user@magetest.com';
        self::$password = '123123';
        self::$roleName = 'Fixture';
        self::createAdminUserFixture();
    }

    /**
     * Tear down the fixtures for the Adminhtml module tests
     *
     * @return void
     **/
    public function tearDown(): void
    {
        self::deleteAdminUserFixture();
    }

    public static function tearDownAfterClass(): void
    {
        self::deleteAdminUserFixture();
    }

    /**
     * Protected function used during testing to authenticate
     * the user ahead of any tests that require the user to be authenticated
     *
     * @param $userName String Username for the admin user
     * @param $password String Password for the supplied account
     *
     * @return void
     **/
    protected function login($userName = null, $password = null)
    {
        $formKey = $this->getFormKey('admin/login');

        if (is_null($userName)) {
            $userName = self::$userName;
        }
        if (is_null($password)) {
            $password = self::$password;
        }
        $this->request->setMethod('POST')
            ->setPost([
                'login' => [
                    'username' => $userName,
                    'password' => $password,
                ],
                'form_key' => $formKey
            ]);

        $this->dispatch('admin/index/login');
    }

    /**
     * Protected function used during testing to clear
     * the authenticated session of the admin user
     *
     * @return void
     * @author Alistair Stead
     **/
    protected function logout()
    {
        $this->dispatch('admin/index/logout');
    }

    /**
     * Create a user in the database to be used
     * during testing
     *
     * @return void
     * @throws Throwable
     * @author Alistair Stead
     **/
    protected static function createAdminUserFixture()
    {
        //create new user
        try {
            $user = Mage::getModel('admin/user')
                ->setData([
                    'username'  => self::$userName,
                    'firstname' => self::$firstName,
                    'lastname'  => self::$lastName,
                    'email'     => self::$email,
                    'password'  => self::$password,
                    'is_active' => 1
                ])->save();

            //create new role
            $role = Mage::getModel('admin/roles')
                ->setName(self::$roleName)
                ->setRoleType('G')
                ->save();

            //give "all" privileges to role
            Mage::getModel('admin/rules')
                ->setRoleId($role->getId())
                ->setResources(["all"])
                ->saveRel();

            $user->setRoleIds([$role->getId()])
                ->setRoleUserId($user->getUserId())
                ->saveRelations();
        } catch (Exception $e) {
            echo "Unable to create fixture :: {$e->getMessage()}";
        }

    }

    /**
     * Delete the user from the database following
     * tests
     *
     * @return void
     **/
    protected static function deleteAdminUserFixture(): void
    {
        if (self::$userName) {
            $users = Mage::getModel('admin/user')->getCollection();
            $users->addFieldToFilter('username', ['eq' => self::$userName]);
            foreach ($users as $user) {
                $user->delete();
            }
        }

        if (self::$roleName) {
            $roles = Mage::getModel('api/roles')->getCollection();
            $roles->addFieldToFilter('role_name', ['eq' => self::$roleName]);
            foreach ($roles as $role) {
                $role->delete();
            }
        }
    }
}

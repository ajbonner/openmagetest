<?php

class SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Member variable that will hold session object
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    /**
     * Setup fixtures and dependencies
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // Bootstrap Mage in the same way as during testing
        $stub = $this->getMockForAbstractClass('MageTest_PHPUnit_Framework_ControllerTestCase');
        $stub->mageBootstrap();

        $this->_session = Mage::getSingleton('admin/session');
    }

    /**
     * Tear down fixtures and dependencies
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset(
            $this->_session
        );
    }

    public function testMageAdminSessionHasBeenPatched()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_Admin_Session',
            $this->_session,
            "The session is of the wrong class"
        );
    }

    public function testSessionLoginDoesNotCallCoreHeaderFunction()
    {
        Mage::getModel('admin/session')->login('admin', 'MageTest123');
        $this->assertEmpty(headers_list());
    }
}
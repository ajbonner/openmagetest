<?php

class MageTest_Core_Model_Email_TemplateTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Setup fixtures and dependencies
     *
     * @return void
     * @author Alistair Stead
     **/
    public function setUp()
    {
        parent::setUp();
        // Bootstrap Mage in the same way as during testing
        $stub = $this->getMockForAbstractClass('MageTest_PHPUnit_Framework_ControllerTestCase');
        $stub->mageBootstrap();
    }
    
    /**
     * Tear down fixtures and dependencies
     *
     * @return void
     * @author Alistair Stead
     **/
    public function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * testingEmailTemplateModelShouldBeReturned
     * @author Alistair Stead
     * @test
     */
    public function testingEmailTemplateModelShouldBeReturned()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_Email_Template',
            Mage::getModel('core/email_template'),
            "MageTest_Core_Model_Email_Template was not returned as expected"
        );
    } // testingEmailTemplateModelShouldBeReturned
}
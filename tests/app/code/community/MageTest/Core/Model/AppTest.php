<?php

class MageTest_Core_Model_AppTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Member variable that will hold the Magento Application
     *
     * @var Mage_Core_App
     **/
    protected $_app;
    
    /**
     * Setup the dependencies for testing Mage_Core_App
     *
     * @return void
     * @author Alistair Stead
     **/
    public function setUp()
    {
        parent::setUp();
        $this->_app = new MageTest_Core_Model_App;
    }
    
    /**
     * Tear down the dependencies and reset state
     *
     * @return void
     * @author Alistair Stead
     **/
    public function tearDown()
    {
        parent::tearDown();
        unset(
            $this->_app
        );
        Mage::reset();
    }
    
    /**
     * mageCoreAppHasBeenPatched
     * @author Alistair Stead
     * @test
     */
    public function mageCoreAppHasBeenPatched()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_App', 
            $this->_app, 
            "The application is of the wrong class"
        );
    } // mageCoreAppHasBeenPatched
    
    /**
     * mageCoreAppGetRequestStillCreatesDependency
     * @author Alistair Stead
     * @test
     */
    public function mageCoreAppGetRequestStillCreatesDependency()
    {
        $this->assertInstanceOf(
            'Mage_Core_Controller_Request_Http',
            $this->_app->getRequest(),
            "The wrong request object is returned"
        );
    } // mageCoreAppGetRequestStillCreatesDependency
    
    /**
     * mageCoreAppGetResponseStillCreatesDependency
     * @author Alistair Stead
     * @test
     */
    public function mageCoreAppGetResponseStillCreatesDependency()
    {
        $this->assertInstanceOf(
            'Mage_Core_Controller_Response_Http',
            $this->_app->getResponse(),
            "The wrong response object is returned"
        );
    } // mageCoreAppGetResponseStillCreatesDependency
    
}
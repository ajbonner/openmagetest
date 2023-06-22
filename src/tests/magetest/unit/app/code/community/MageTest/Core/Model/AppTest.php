<?php

namespace app\code\community\MageTest\Core\Model;

use MageTest_Core_Model_App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    /**
     * Member variable that will hold the Magento Application
     *
     * @var MageTest_Core_Model_App
     */
    protected $_app;

    /**
     * Setup the dependencies for testing Mage_Core_App
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->_app = new MageTest_Core_Model_App();
    }

    /**
     * Tear down the dependencies and reset state
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset(
            $this->_app
        );
    }

    /**
     * @test
     */
    public function mageCoreAppHasBeenPatched()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_App',
            $this->_app,
            "The application is of the wrong class"
        );
    }
}

class_alias(AppTest::class, 'MageTest_Core_Model_AppTest', false);

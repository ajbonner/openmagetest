<?php
/**
 * Magento Cms Helper Page tests
 *
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_Cms_IndexControllerTest
 *
 * @package    Mage_Cms
 * @subpackage Mage_Cms_Test
 *
 *
 * @uses PHPUnit_Framework_Magento_TestCase
 */
class Mage_Cms_Helper_PageTest extends MageTest_PHPUnit_Framework_TestCase {

    /**
     * Member variable for the Page Helper
     *
     * @var Mage_Cms_Helper_Page
     **/
    protected $_helper;

    /**
     * Setup fictures and dependencies
     *
     * @return void
     * @author Alistair Stead
     **/
    public function setUp(): void
    {
        parent::setUp();
        $this->_helper = Mage::helper('cms/page');
    }

    /**
     * Teardown fictures and dependencies
     *
     * @return void
     * @author Alistair Stead
     **/
    public function tearDown(): void
    {
        parent::tearDown();
        unset(
            $this->_helper
        );
    }

    /**
     * getPageUrlShouldReturnString
     * @author Alistair Stead
     * @test
     */
    public function getPageUrlShouldReturnString()
    {
        $this->markTestIncomplete(
                  'This test has not been implemented yet.'
                );
        // $this->assertTrue(
        //     is_string($this->_helper->getPageUrl()),
        //     "getPageUrl does not return a string value"
        // );
    } // getPageUrlShouldReturnString

}

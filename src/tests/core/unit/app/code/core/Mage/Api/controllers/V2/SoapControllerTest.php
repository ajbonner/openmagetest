<?php
/**
 * Magento API V2 Soap Controller tests
 *
 * @package    Mage_Api
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_Api_V2_SoapControllerTest
 *
 * @package    Mage_Api
 * @subpackage Mage_Api_Test
 */
class Mage_Api_V2_SoapControllerTest extends MageTest_PHPUnit_Framework_ControllerTestCase {

    /**
     * @test
     */
    public function theFullApiV2RouteUsesV2SoapController()
    {
        $this->dispatch('/api/v2_soap?wsdl=1');

        $this->assertAction('index', "The index action is not used");
        $this->assertController('v2_soap', "The expected controller is not been used");

        $this->assertHeaderContains('Content-Type', 'text/xml', "The Content-Type header is not text/xml as expected");
    } // theFullAPIV2RouteUsesV2SoapController
}

<?php
/**
 * Magento API Soap Controller tests
 *
 * @package    Mage_Api
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_Api_SoapControllerTest
 *
 * @package    Mage_Api
 * @subpackage Mage_Api_Test
 *
 */
class Mage_Api_SoapControllerTest extends MageTest_PHPUnit_Framework_ControllerTestCase {

    /**
     * @test
     */
    public function theFullApiSoapRouteUsesSoapController()
    {
        $this->dispatch('/api/soap?wsdl=1');

        $this->assertAction('index', "The index action is not used");
        $this->assertController('soap', "The expected controller is not been used");

        $this->assertHeaderContains('Content-Type', 'text/xml', "The Content-Type header is not text/xml as expected");
    } // theFullAPISoapRouteUsesSoapController
}

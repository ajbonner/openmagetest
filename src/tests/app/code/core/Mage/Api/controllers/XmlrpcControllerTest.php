<?php
/**
 * Magento API XML-RPC Controller tests
 *
 * @package    Mage_Api
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_Api_XmlrpcControllerTest
 *
 * @package    Mage_Api
 * @subpackage Mage_Api_Test
 *
 *
 * @uses PHPUnit_Framework_Magento_TestCase
 */
class Mage_Api_XmlrpcControllerTest extends MageTest_PHPUnit_Framework_ControllerTestCase {
    
    /**
     * theFullAPISoapRouteUsesXmlprcController
     * 
     * 
     * 
     * @author Alistair Stead
     * @test
     */
    public function theFullAPISoapRouteUsesXmlprcController()
    {
        $this->dispatch('api/xmlrpc/');
        
        $this->assertAction('index', "The index action is not used");
        $this->assertController('xmlrpc', "The expected controller is not been used");
        
        $this->assertHeaderContains('Content-Type', 'text/xml', "The Content-Type header is not text/xml as expected");
    } // theFullAPISoapRouteUsesXmlprcController
}
<?php
/**
 * Magento CatalogSearch ResultController tests
 *
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Mage_CatalogSearch_ResultControllerTest
 *
 * @package    Mage_CatalogSearch
 * @subpackage Mage_CatalogSearch_Test
 */
class Mage_CatalogSearch_ResultControllerTest extends MageTest_PHPUnit_Framework_ControllerTestCase {

    /**
     * @test
     */
    public function indexActionShouldRedirectToReferrerWithEmptyQuery()
    {
        $formKey = $this->getFormKey('catalogsearch/index');

        $this->request->setMethod('POST')
            ->setPost(['q' => '', 'form_key' => $formKey]);

        $this->dispatch('catalogsearch/result/index');

        $this->assertResponseCode('302', 'The response code is not 302');
    }

    /**
     * @test
     */
    public function indexActionShouldReturn200WithValidQuery()
    {
        $formKey = $this->getFormKey('catalogsearch/index');

        $this->request->setMethod('POST')
            ->setPost(['q' => 'foo', 'form_key' => $formKey]);

        $this->dispatch('catalogsearch/result/index');

        $this->assertResponseCode('200', 'The response code is not 200');
    }
}

<?php

class Mage_Catalog_CategoryControllerTest extends MageTest_PHPUnit_Framework_ControllerTestCase {

    /**
     * Member variable that will hold the Category Helper
     *
     * @var Mage_Catalog_Helper_Category
     **/
    protected $_helperCategory;

    /**
     * Setup dependencies for tests
     *
     * @return void
     **/
    public function setUp(): void
    {
        parent::setUp();
        $this->_helperCategory = Mage::helper('catalog/category');
    }

    /**
     * Reset dependencies
     *
     * @return void
     **/
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_helperCategory);
    }

    /**
     * allCategoryURLsShouldRespond
     * @author Alistair Stead
     * @test
     */
    public function allCategoryURLsShouldRespond()
    {
        $categories = $this->_helperCategory->getStoreCategories(true, true, true);

        foreach ($categories as $key => $category) {
            if ($this->_helperCategory->canShow($category) && $category->getIsActive()) {
                // Construct the URL for the category pre dispatch
                $url = str_replace(Mage::getBaseUrl(), '', $this->_helperCategory->getCategoryUrl($category));
                // Dispatch the testing URL
                $this->dispatch($url);
                // Make assertions about the response
                $this->assertRoute('catalog', "The expected category route has not been matched");
                $this->assertAction('view', "The index action has not been called");
                $this->assertController('category', "The expected controller is not been used");
                // Test that the h1 tag is correctly populated with the category name
                $this->assertQueryContentContains(
                    'h1',
                    $category->getName(),
                    'The page h1 has not been correctly set'
                );
                // Reset after testing the response
                $this->reset();
            }
        }
    } // allCategoryURLsShouldRespond

    /**
     * invalidCategoryShouldReturn404
     * @author Alistair Stead
     * @test
     */
    public function invalidCategoryShouldReturn404()
    {
        $this->dispatch('invalid-category.html');

        // TODO this should be a 302 redirect followed by a 404 need to
        // update response to follow redirects like lime in symfony
        // $this->assertResponseCode('404', "The invalid request has not returned 404");
        $this->markTestIncomplete(
                  'This test has not been implemented yet.'
                );

    } // invalidCategoryShouldReturn404
}

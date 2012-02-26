<?php
require_once 'mink/autoload.php';

set_include_path(dirname(__FILE__) . '/../../../lib' . PATH_SEPARATOR . get_include_path());
// require_once dirname(__FILE__) . '/../../../lib/MageTest/Mink/Behat/Context/MinkContext.php';

use MageTest\Behat\Context\BehatContext;
use MageTest\Mink\Behat\Context\MageMinkContext;

/**
 * Features context.
 */
class FeatureContext extends MageMinkContext
{
    /**
     * @Then /^I wait/
     */
    public function iWait()
    {
        $this->getSession()->wait(5000);
    }

    /**
     * Checks, that current page response is cached in the Gateway Cache.
     *
     * @Then /^the response is a cache hit$/
     */
    public function assertResponseCacheHit()
    {
        $headers = $this->getSession()->getResponseHeaders();
        try {
            $actual = $headers['X-Cache'];
            assertEquals($actual, 'HIT');
        } catch (AssertException $e) {
            $message = sprintf('Current response status code is %s, but HIT expected', $actual);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that current page response is not cached in the Gateway Cache.
     *
     * @Then /^the response is a cache miss$/
     */
    public function assertResponseCacheMiss()
    {
        $headers = $this->getSession()->getResponseHeaders();
        try {
            $actual = $headers['X-Cache'];
            assertEquals($actual, 'MISS');
        } catch (AssertException $e) {
            $message = sprintf('Current response status code is %s, but MISS expected', $actual);
            throw new ExpectationException($message, $this->getSession(), $e);
        }
    }
}
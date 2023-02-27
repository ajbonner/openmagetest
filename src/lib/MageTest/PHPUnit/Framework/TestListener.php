<?php
/**
 * Mage-Test
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, that is bundled with this
 * package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 *
 * http://opensource.org/licenses/MIT
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email
 * to <magetest@sessiondigital.com> so we can send you a copy immediately.
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @copyright  Copyright (c) 2012-2013 MageTest team and contributors.
 */

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

/**
 * MageTest_TestListener
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
class MageTest_PHPUnit_Framework_TestListener implements TestListener
{
    /**
     * An error occurred.
     *
     * @param  Test $test
     * @param  Throwable              $t
     * @param  float                  $time
     */
    public function addError(Test $test, Throwable $t, $time): void
    {
    }

    /**
     * A failure occurred.
     *
     * @param  Test                 $test
     * @param  AssertionFailedError $e
     * @param  float                $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time): void
    {
    }


    public function addWarning(Test $test, Warning $e, float $time): void
    {
        // TODO: Implement addWarning() method.
    }

    /**
     * Incomplete test.
     *
     * @param  Test $test
     * @param  Throwable              $t
     * @param  float                  $time
     */
    public function addIncompleteTest(Test $test, \Throwable $t, $time): void
    {
    }

    /**
     * Risky test.
     *
     * @param Test $test
     * @param Throwable $t
     * @param float $time
     * @since  Method available since Release 4.0.0
     */
    public function addRiskyTest(Test $test, Throwable $t, $time): void
    {
    }

    /**
     * Skipped test.
     *
     * @param  Test $test
     * @param  Throwable              $t
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(Test $test, Throwable $t, $time): void
    {
    }

    /**
     * A test suite started.
     *
     * @param  TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(TestSuite $suite): void
    {
        // Flush the cache once on execution rather than on every test
        MageTest_Util_Cache::flush();
    }

    /**
     * A test suite ended.
     *
     * @param  TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * A test started.
     *
     * @param  Test $test
     */
    public function startTest(Test $test): void
    {
    }

    /**
     * A test ended.
     *
     * @param  Test $test
     * @param  float                  $time
     */
    public function endTest(Test $test, $time): void
    {
    }
}

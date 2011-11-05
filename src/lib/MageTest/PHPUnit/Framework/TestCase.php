<?php
/**
 * Magento PHPUnit TestCase
 *
 * @package     MageTest_PHPUnit
 * @copyright   Copyright (c) 2011 Ibuildings. (http://www.ibuildings.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Alistair Stead <alistair@ibuildings.com>
 * @version     $Id$
 */

/**
 * MageTest_PHPUnit_Framework_TestCase
 *
 * @category    MageTest
 * @package     MageTest_PHPUnit
 * @subpackage  MageTest_PHPUnit_TestCase
 * @uses        PHPUnit_Framework_TestCase
 */
abstract class MageTest_PHPUnit_Framework_TestCase extends PHPUnit_Framework_TestCase {

    public function setUp() {
        parent::setUp();

        $bootstrap = new MageTest_PHPUnit_Bootstrap;
        $bootstrap->init();
    }
}
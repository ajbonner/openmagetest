<?php
/**
 * Magento Unit Test Object factory
 *
 * @category   Integration Tests
 * @package    MageTestMageTest_Fixture
 * @copyright  Copyright (c) 2011 Ibuildings
 * @version    $Id$
 */

/**
 * MageTest_Fixture
 *
 * @category   Integration Tests
 * @package    MageTest
 * @subpackage MageTest_Fixture
 * @author Alistair Stead
 */
abstract class MageTest_Fixture
{
    abstract static function create(array $data = array());
}
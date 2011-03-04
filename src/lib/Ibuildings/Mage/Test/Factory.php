<?php
/**
 * Magento Unit Test Object factory
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @copyright  Copyright (c) 2011 Ibuildings
 * @version    $Id$
 */

/**
 * Ibuildings_Mage_Test_Factory
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @subpackage Ibuildings_Mage_Test_Factory
 * @author Alistair Stead
 */
abstract class Ibuildings_Mage_Test_Factory
{
    abstract static function build(array $data = array());
}
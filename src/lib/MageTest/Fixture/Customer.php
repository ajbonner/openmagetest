<?php
/**
 * Magento Unit Test Object factory Customer
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Ibuildings_Mage_Test_Factory_Customer
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @subpackage Ibuildings_Mage_Test_Factory
 * @author Alistair Stead
 */
class MageTest_Fixture_Customer extends MageTest_Fixture
{
  /**
   * Helper funtion to constuct a customer to be used for testing
   *
   * @return Mage_Customer_Customer
   * @author Alistair Stead
   **/
    public static function create(array $data = array())
    {
        $_data = array(
            'prefix' => 'Mr',
            'firstname' => 'Fixture',
            'middlename' => 'C',
            'lastname' => 'User',
            'email' => 'fixture.c.user@magento.com',
            'suffix' => 'Phd',
            'dob' => '07/03/1977',
            'taxvat' => '000226677',
            'gender' => 'male'
        );

        $data = array_merge($_data, $data);

        $customer = Mage::getModel('customer/customer');
        $customer->setData($data);
        $customer->setPassword($customer->generatePassword(8));

        return $customer;
    }
}
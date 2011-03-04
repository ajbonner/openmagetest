<?php
/**
 * Magento Unit Test Object factory Product
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @copyright  Copyright (c) 2010 Ibuildings
 * @version    $Id$
 */

/**
 * Test_Helper_Product
 *
 * @category   Integration Tests
 * @package    Ibuildings_Mage_Test
 * @subpackage Ibuildings_Mage_Test_Factory
 * @author Alistair Stead
 */
class Ibuildings_Mage_Test_Factory_Product extends Ibuildings_Mage_Test_Factory
{
    /**
    * Helper funtion to constuct a product to be used for testing
    *
    * @param $type string simple|grouped|configurable|virtual|bundle|downloadable|giftcard
    * @return Mage_Catalog_Product
    * @author Alistair Stead
    **/
    public static function build($type = 'simple', array $data = array())
    {
        $_data = array(
            'sku' => '9999999',
            'name' => 'fixture',
            'attribute_set' => 'Default',
            'attribute_set_id' => 4, // added to fix DB constraints problem
            'qty' => '100',
            'min_qty' => '10',
            'is_in_stock' => '1',
            'use_config_manage_stock' => '1',
            'manage_stock' => '1',
            'status' => 'Enabled',
            'weight' => '140',
            'visibility' => '1',
            'price' => '150.00',
            'remove_from_back_order' => '1',
        );

        $data = array_merge($_data, $data);

        $product = Mage::getModel('catalog/product');
        $product->fromArray($data);
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);
        $product->setTypeId($type);
        $product->setTypeInstance(Mage::getSingleton('catalog/product_type')
            ->factory($product, true), true);

        return $product;
    }
}
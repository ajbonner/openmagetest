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
    public static function build(array $data = array())
    {
        $_data = array(
            'store' => 'default',
            'website' => 'base',
            'attribute_set' => 'Default',
            'type' => 'simple',
            'category_ids' => self::_getCategoryIds(),
            'sku' => time(),
            'has_options' => '0',
            'price' => '150.00',
            'cost' => '',
            'weight' => '140',
            'minimal_price' => '',
            'status' => 'Enabled',
            'tax_class_id' => 'Taxable Goods',
            'visibility' => 'Catalog, Search', 
            'name' => 'Fixture',
            'url_key' => 'fixture'.time(),
            'meta_title' => 'Fixture',
            'meta_description' => 'Fixture',
            'description' => 'Fixture',
            'meta_keyword' => 'Fixture',
            'short_description' => 'Fixture',
            'qty' => '10000',
            'min_qty' => '1',
            'use_config_min_qty' => '1',
            'is_qty_decimal' => '1',
            'backorders' => '0',
            'use_config_manage_stock' => '1',
            'min_sale_qty' => '1',
            'use_config_min_sale_qty' => '1',
            'max_sale_qty' => '100',
            'use_config_max_sale_qty' => '1',
            'is_in_stock' => '1',
            'notify_stock_qty' => '0',
            'use_config_notify_stock_qty' => '1',
            'manage_stock' => '1',
            'use_config_manage_stock' => '1',
            'store_id' => '0',
            'product_type_id' => 'simple'
        );

        $data = array_merge($_data, $data);

        $productAdapter = Mage::getModel('catalog/convert_adapter_product');
        $productAdapter->saveRow($data);
        
        $product = Mage::getModel('catalog/product');
        $productId = $product->getIdBySku($_data['sku']);
        $product->load($productId);
        
        return $product;
    }
    
    /**
     * Retrieve category ids
     *
     * @return array
     */
    protected static function _getCategoryIds()
    {
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
        $urlAttributeId = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_category', 'url_path')
            ->getId();
        $select = $adapter->select()
            ->from(array('e' => Mage::getSingleton('core/resource')->getTableName('catalog/category')), 'entity_id')
            ->join(
                array('ev' => Mage::getSingleton('core/resource')->getTableName('catalog/category') . '_varchar'),
                'e.entity_id = ev.entity_id AND ev.attribute_id=' . $urlAttributeId,
                array('value'));
        $select->where('e.level>?', '1');

        $categoryIds = $adapter->fetchPairs($select);

        return implode(',', array_keys($categoryIds));
    }
    
}
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

/**
 * MageTest_PHPUnit_Framework_TestCase
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
abstract class MageTest_PHPUnit_Framework_TestCase extends PHPUnit_Framework_TestCase 
{
    static $bootstrapped = false;

    /**
     * @var MageTest_Bootstrap
     */
    protected $bootstrap;

    public function setUp()
    {
        parent::setUp();

        $this->mageBootstrap();
    }

    /**
     * Returns a mock object for the specified model alias.
     *
     * @param  string  $modelAlias
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @param  boolean $cloneArguments
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     */
    public function getModelMock($modelAlias, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE)
    {
        return $this->getMock(
            Mage::getConfig()->getModelClassName($modelAlias),
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload
        );
    }

    /**
     * Returns a mock object for the specified block alias.
     *
     * @param  string  $blockAlias
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @param  boolean $cloneArguments
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     */
    public function getBlockMock($blockAlias, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE)
    {
        return $this->getMock(
            Mage::getConfig()->getBlockClassName($blockAlias),
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload
        );
    }

    /**
     * Returns a mock object for the specified helper alias.
     *
     * @param  string  $helperAlias
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @param  boolean $cloneArguments
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     */
    public function getHelperMock($helperAlias, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE)
    {
        return $this->getMock(
            Mage::getConfig()->getHelperClassName($helperAlias),
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload
        );
    }

    /**
     * Returns a mock object for the specified resource model alias.
     *
     * @param  string  $resourceAlias
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @param  boolean $cloneArguments
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     */
    public function getResourceModelMock($resourceAlias, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE)
    {
        return $this->getMock(
            Mage::getConfig()->getResourceModelClassName($resourceAlias),
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload
        );
    }

    /**
     * @return MageTest_Bootstrap
     */
    public function mageBootstrap()
    {
        $_SERVER['MAGE_TEST'] = true;

        $this->bootstrap = new MageTest_Bootstrap();
        $this->bootstrap->init();

        if (!self::$bootstrapped) {
            $this->bootstrapEventAreaParts($this->bootstrap, array(
                Mage_Core_Model_App_Area::AREA_GLOBAL,
                Mage_Core_Model_App_Area::AREA_ADMIN,
                Mage_Core_Model_App_Area::AREA_FRONTEND,
                Mage_Core_Model_App_Area::AREA_ADMINHTML));
        }

        return $this->bootstrap;
    }

    /**
     * @return MageTest_Bootstrap
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * Loads events config part for areas included in $areas
     *
     * @param MageTest_Bootstrap $bootstrap
     * @param string[] $areas
     *
     * @return MageTest_PHPUnit_Framework_TestCase
     */
    protected function bootstrapEventAreaParts($bootstrap, $areas)
    {
        foreach ($areas as $area) {
            $bootstrap->app()->loadAreaPart(
                $area,
                Mage_Core_Model_App_Area::PART_EVENTS);
        }

        return $this;
    }
}

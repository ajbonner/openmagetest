<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

abstract class MageTest_PHPUnit_Framework_TestCase extends TestCase
{
    private static bool $bootstrapped = false;

    protected MageTest_Bootstrap $bootstrap;

    public function setUp(): void
    {
        parent::setUp();
        $this->mageBootstrap();
    }

    /**
     * Returns a mock object for the specified model alias.
     *
     * @param  string  $modelAlias
     * @param  ?array $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  bool $callOriginalConstructor
     * @param  bool $callOriginalClone
     * @param  bool $callAutoload
     * @return MockObject
     * @throws \PHPUnit\Framework\Exception
     */
    public function getModelMock(
        string $modelAlias,
        ?array $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true
    ): MockObject {
        return $this->buildMock(Mage::getConfig()->getModelClassName($modelAlias), $methods, $arguments, $mockClassName,
            $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Returns a mock object for the specified block alias.
     *
     * @param  string  $blockAlias
     * @param  ?array  $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  bool $callOriginalConstructor
     * @param  bool $callOriginalClone
     * @param  bool $callAutoload
     * @return MockObject
     * @throws \PHPUnit\Framework\Exception
     */
    public function getBlockMock(
        string $blockAlias,
        ?array $methods = [],
        array $arguments = array(),
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true
    ): MockObject {
        return $this->buildMock(Mage::getConfig()->getBlockClassName($blockAlias), $methods, $arguments, $mockClassName,
            $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Returns a mock object for the specified helper alias.
     *
     * @param  string  $helperAlias
     * @param  ?array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  bool $callOriginalConstructor
     * @param  bool $callOriginalClone
     * @param  bool $callAutoload
     * @return MockObject
     * @throws \PHPUnit\Framework\Exception
     */
    public function getHelperMock(
        string $helperAlias,
        ?array $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true
    ): MockObject {
        return $this->buildMock(Mage::getConfig()->getHelperClassName($helperAlias), $methods, $arguments, $mockClassName,
            $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Returns a mock object for the specified resource model alias.
     *
     * @param  string  $resourceAlias
     * @param  ?array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  bool $callOriginalConstructor
     * @param  bool $callOriginalClone
     * @param  bool $callAutoload
     * @return MockObject
     * @throws \PHPUnit\Framework\Exception
     */
    public function getResourceModelMock(
        $resourceAlias,
        ?array $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true
    ): MockObject {
        return $this->buildMock(Mage::getConfig()->getResourceModelClassName($resourceAlias), $methods, $arguments,
            $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    public function mageBootstrap(): MageTest_Bootstrap
    {
        $_SERVER['MAGE_TEST'] = true;

        $this->bootstrap = new MageTest_Bootstrap();
        $this->bootstrap->init();

        if (!self::$bootstrapped) {
            $this->bootstrapEventAreaParts($this->bootstrap, [
                Mage_Core_Model_App_Area::AREA_GLOBAL,
                Mage_Core_Model_App_Area::AREA_ADMIN,
                Mage_Core_Model_App_Area::AREA_FRONTEND,
                Mage_Core_Model_App_Area::AREA_ADMINHTML]
            );
        }

        return $this->bootstrap;
    }

    public function getBootstrap(): MageTest_Bootstrap
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
    protected function bootstrapEventAreaParts(MageTest_Bootstrap $bootstrap, array $areas): self
    {
        foreach ($areas as $area) {
            $bootstrap->app()->loadAreaPart(
                $area,
                Mage_Core_Model_App_Area::PART_EVENTS
            );
        }

        return $this;
    }

    private function buildMock(
        $className,
        ?array $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true
    ): MockObject {
        $builder = $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs($arguments)
            ->setMockClassName($mockClassName);

        if (! $callOriginalConstructor) {
            $builder->disableOriginalConstructor();
        }

        if (! $callOriginalClone) {
            $builder->disableOriginalClone();
        }

        if (! $callAutoload) {
            $builder->disableAutoload();
        }

        return $builder->getMock();
    }
}

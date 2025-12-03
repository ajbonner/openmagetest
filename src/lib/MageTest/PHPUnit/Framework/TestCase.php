<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

abstract class MageTest_PHPUnit_Framework_TestCase extends TestCase
{
    private static ?MageTest_Bootstrap $sharedBootstrap = null;
    private static array $pendingSingletonCleanup = [];
    private static ?Mage_Core_Model_Resource $realCoreResource = null;
    protected MageTest_Bootstrap $bootstrap;
    private Mage_Core_Model_Config $config;
    private array $singletonMocks = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->mageBootstrap();
        Mage::getConfig()->resetTestState();
        $this->resetRegistry();
        Mage::app()->resetDispatchedEvents();
    }

    public function tearDown(): void
    {
        // Close DB connections to prevent "too many connections" errors
        try {
            $resource = Mage::getSingleton('core/resource');
            if ($resource) {
                foreach ($resource->getConnections() as $connection) {
                    try {
                        $connection->closeConnection();
                    } catch (Exception $e) {}
                }
            }
        } catch (Exception $e) {
            // Ignore errors during cleanup
        }
        parent::tearDown();
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
        array $arguments = [],
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

        // Reuse the bootstrap instance across tests to avoid resource leaks
        if (self::$sharedBootstrap === null) {
            self::$sharedBootstrap = new MageTest_Bootstrap();
            self::$sharedBootstrap->init();

            $this->bootstrapEventAreaParts(self::$sharedBootstrap, [
                Mage_Core_Model_App_Area::AREA_GLOBAL,
                Mage_Core_Model_App_Area::AREA_ADMIN,
                Mage_Core_Model_App_Area::AREA_FRONTEND,
                Mage_Core_Model_App_Area::AREA_ADMINHTML]
            );

            // Save reference to the real core/resource singleton before any test can mock it
            self::$realCoreResource = Mage::getSingleton('core/resource');
        }

        $this->bootstrap = self::$sharedBootstrap;

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

    /**
     * @param string $observerName e.g. mymodulens_mymodule_does_something_on_order_place
     * @param string $eventName e.g. a dispatched event e.g. sales_order_place_after
     * @param string $area e.g. global/frontend/admin/adminhtml
     * @return $this
     */
    public function assertObserverReceivesEvent($observerName, $eventName, $area = 'global'): self
    {
        $config = $this->getAppConfig();
        $this->whitelistObserver($observerName);
        $eventConfig = $config->getEventConfig($area, $eventName);

        if (isset($eventConfig->observers->$observerName)) {
            $observer = $eventConfig->observers->$observerName;
            $class = (string)$observer->class;
            $mock = $this->getModelMock($class);
            $mock->expects($this->once())
                ->method((string)$observer->method);

            $this->setModelInstanceMock($class, $mock);
            Mage::dispatchEvent($eventName, []);
        } else {
            $this->fail('No observer called ' . $observerName . ' is listening for event ' . $eventName . ' in area ' . $area);
        }

        return $this;
    }

    public function getModelClassName(string $modelGroupName): string
    {
        return $this->getAppConfig()->getModelClassName($modelGroupName);
    }

    public function getResourceModelClassName(string $resourceModelGroupName): string
    {
        return $this->getAppConfig()->getResourceModelClassName($resourceModelGroupName);
    }

    /**
     * @param string $class
     * @param MockObject $mock
     * @return $this
     */
    public function setModelInstanceMock(string $class, $mock): self
    {
        $this->getAppConfig()->setModelInstanceMock($class, $mock);
        return $this;
    }

    /**
     * @param string $class
     * @param MockObject $mock
     * @return $this
     */
    public function setObserverMock(string $class, $mock): self
    {
        $className = str_contains($class, '/')
            ? $this->getModelClassName($class) : $class;
        $this->setModelInstanceMock($className, $mock);
        return $this;
    }

    /**
     * @param string $class
     * @param MockObject $mock
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setHelperMock($class, $mock): self
    {
        $registryKey = '_helper/' . $class;

        if (Mage::registry($registryKey)) {
            Mage::unregister($registryKey);
        }

        Mage::register($registryKey, $mock);
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function resetHelperMock($class): self
    {
        $registryKey = '_helper/' . $class;

        if (Mage::registry($registryKey)) {
            Mage::unregister($registryKey);
        }

        return $this;
    }

    /**
     * @param string $class
     * @param MockObject $mock
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setSingletonMock(string $class, $mock): self
    {
        $registryKey = '_singleton/' . $class;

        if (Mage::registry($registryKey)) {
            Mage::unregister($registryKey);
        }

        Mage::register($registryKey, $mock);
        $this->singletonMocks[] = $registryKey;
        // Track in static property for cleanup by next test
        self::$pendingSingletonCleanup[$registryKey] = true;
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function resetSingletonMock(string $class): self
    {
        $registryKey = '_singleton/' . $class;

        if (Mage::registry($registryKey)) {
            Mage::unregister($registryKey);
        }

        return $this;
    }

    /**
     * @param string $observer
     * @return $this
     */
    public function whitelistObserver($observer): self
    {
        $this->getAppConfig()->whitelistObserver($observer);
        return $this;
    }

    public function disableEvent(string $eventName): void
    {
        $this->getAppConfig()->disableEvent($eventName);
    }

    /**
     * @param string $eventName
     * @return int
     */
    public function getDispatchedEventCount(string $eventName)
    {
        return Mage::app()->getDispatchedEventCount($eventName);
    }

    public function setAppConfig(Mage_Core_Model_Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    private function getAppConfig(): Mage_Core_Model_Config
    {
        if (! isset($this->config)) {
            $this->config = Mage::getConfig();
        }

        return $this->config;
    }

    /**
     * Clear all helper mocks and tracked singleton mocks from the Mage registry to ensure test isolation.
     * Only singleton mocks explicitly set via setSingletonMock() are cleared, as other singletons
     * may hold required framework state (e.g., core/resource).
     */
    private function resetRegistry(): void
    {
        // Clean up any singleton mocks from previous tests (static tracking)
        foreach (array_keys(self::$pendingSingletonCleanup) as $key) {
            if (Mage::registry($key)) {
                Mage::unregister($key);
            }
        }
        self::$pendingSingletonCleanup = [];

        // Restore the real core/resource singleton to preserve its connections
        // This is critical for transaction rollback to work properly
        if (self::$realCoreResource !== null) {
            $registryKey = '_singleton/core/resource';
            if (Mage::registry($registryKey)) {
                Mage::unregister($registryKey);
            }
            Mage::register($registryKey, self::$realCoreResource);
        }

        $reflection = new ReflectionClass(Mage::class);
        $registryProperty = $reflection->getProperty('_registry');
        $registryProperty->setAccessible(true);
        $registry = $registryProperty->getValue();

        foreach (array_keys($registry) as $key) {
            if (str_starts_with($key, '_helper/')) {
                Mage::unregister($key);
            }
            // Clear eav/config singleton to prevent stale attribute caches
            if ($key === '_singleton/eav/config') {
                Mage::unregister($key);
            }
        }

        $this->singletonMocks = [];
    }
}

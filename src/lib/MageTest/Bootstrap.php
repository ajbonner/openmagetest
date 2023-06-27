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
 * MageTest_Bootstrap
 *
 * @category    MageTest
 * @package     Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
class MageTest_Bootstrap
{
    /**
     * Reflection class of Mage
     *
     * @var string
     **/
    protected $_mageReflection;

    /**
     * Reflection class of Mage_Core_Model_App
     *
     * @var ReflectionClass
     **/
    protected $_appReflection;

    /**
     * undocumented function
     *
     * @author Alistair Stead
     **/
    public function __construct()
    {
        $this->isValid();
        $mageReflection = $this->getMageReflection();
        $_app = $mageReflection->getProperty('_app');
        $_app->setAccessible(true);
    }

    /**
     * Bootstrap the Mage application in a similar way to the procedure
     * of index.php
     *
     * Then sets test case request and response objects in Mage_Core_App,
     * and disables returning the response.
     *
     * @param string $code
     * @param string $type
     * @param array $options
     * @param array $modules
     * @return void
     * @author Alistair Stead
     */
    public function init($code = '', $type = 'store', $options = [], $modules = [])
    {
        Mage::reset();
        Mage::setRoot();
        $this->setProtectedProperty('_app', new MageTest_Core_Model_App());
        $this->setProtectedProperty('_config', new MageTest_Core_Model_Config($options));

        if (!empty($modules)) {
            $this->getProtectedPropertyValue('_app')->initSpecified($code, $type, $options, $modules);
        } else {
            $this->getProtectedPropertyValue('_app')->init($code, $type, $options);
        }

        $app = $this->app();
        $app->setRequest(new MageTest_Controller_Request_HttpTestCase());
        $app->setResponse(new MageTest_Controller_Response_HttpTestCase());
    }

    /**
     * undocumented function
     *
     * @param string $code
     * @param string $type
     * @param array $options
     * @return void
     * @author Alistair Stead
     **/
    public function run($code = '', $type = 'store', $options = [])
    {
        Varien_Profiler::start('mage');
        Mage::setRoot();
        $this->setProtectedProperty('_app', new MageTest_Core_Model_App());
        $this->setProtectedProperty('_events', new Varien_Event_Collection);
        $this->setProtectedProperty('_config', new MageTest_Core_Model_Config($options));
        $this->getProtectedPropertyValue('_app')->run([
            'scope_code' => $code,
            'scope_type' => $type,
            'options'    => $options,
        ]);

        $app = $this->app();
        $app->setRequest(new MageTest_Controller_Request_HttpTestCase());
        $app->setResponse(new MageTest_Controller_Response_HttpTestCase());

        Varien_Profiler::stop('mage');
    }

    /**
     * undocumented function
     *
     * @param string $code
     * @param string $type
     * @param array $options
     * @return MageTest_Core_Model_App
     * @author Alistair Stead
     **/
    public function app($code = '', $type = 'store', $options = [])
    {
        if (is_null($this->getProtectedPropertyValue('_app'))) {
            Mage::setRoot();
            $this->setProtectedProperty('_app', new MageTest_Core_Model_App());
            $this->setProtectedProperty('_events', new Varien_Event_Collection());
            $this->setProtectedProperty('_config', new MageTest_Core_Model_Config($options));

            Varien_Profiler::start('self::app::init');
            $this->getProtectedPropertyValue('_app')->init($code, $type, $options);
            Varien_Profiler::stop('self::app::init');
            $this->getProtectedPropertyValue('_app')->loadAreaPart(
                Mage_Core_Model_App_Area::AREA_GLOBAL,
                Mage_Core_Model_App_Area::PART_EVENTS
            );
        }
        return $this->getProtectedPropertyValue('_app');
    }

    /**
     * undocumented function
     *
     * @return ReflectionClass
     * @author Alistair Stead
     **/
    public function getAppReflection()
    {
        if (is_null($this->_appReflection)) {
            $this->_appReflection = new ReflectionClass('Mage_Core_Model_App');
        }
        return $this->_appReflection;
    }

    /**
     * undocumented function
     *
     * @return ReflectionClass
     * @author Alistair Stead
     **/
    public function getMageReflection()
    {
        if (is_null($this->_mageReflection)) {
            $this->_mageReflection = new ReflectionClass('Mage');
        }
        return $this->_mageReflection;
    }

    /**
     * undocumented function
     *
     * @param string $name
     * @return ReflectionProperty
     * @author Alistair Stead
     **/
    public function getProtectedProperty($name)
    {
        $property = $this->getMageReflection()->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * undocumented function
     *
     * @param string $name
     * @return mixed
     * @author Alistair Stead
     **/
    public function getProtectedPropertyValue($name)
    {
        return $this->getProtectedProperty($name)->getValue();
    }

    /**
     * undocumented function
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @author Alistair Stead
     **/
    public function setProtectedProperty($name, $value)
    {
        $property = $this->getProtectedProperty($name);
        $property->setValue($value);
    }

    /**
     * Validate that we are able to run unit tests
     *
     * @return boolean
     * @author Alistair Stead
     * @throws DomainException
     **/
    public function isValid()
    {
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            throw new DomainException(
                sprintf(
                    "MageTest can only function with a PHP version greater than 5.3.x, %s is installed",
                    PHP_VERSION
                )
            );
        }

        return true;
    }
}

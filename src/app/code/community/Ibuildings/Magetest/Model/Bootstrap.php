<?php
/**
 * Magento PHPUnit Bootstrap
 *
 * @package     Ibuildings_Mage_Test_PHPUnit
 * @copyright   Copyright (c) 2011 Ibuildings. (http://www.ibuildings.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Alistair Stead <alistair@ibuildings.com>
 * @version     $Id$
 */

/**
 * Ibuildings_MageTest_PHPUnit_Bootstrap
 *
 * @category    Mage_Test
 * @package     Ibuildings_Mage_Test_PHPUnit
 * @subpackage  Ibuildings_Mage_Test_PHPUnit
 * @uses        PHPUnit_Framework_TestCase
 */
class Ibuildings_MageTest_PHPUnit_Bootstrap {
    
    /**
     * Reflection class of Mage_Core_Model_App
     *
     * @var ReflectionClass
     **/
    protected $_appReflection;
    
    /**
     * Bootstrap the Mage application in a similar way to the procedure
     * of index.php
     * 
     * Then sets test case request and response objects in Mage_Core_App,
     * and disables returning the response.
     *
     * @return void
     * @author Alistair Stead
     */
    public function init()
    {
        $this->isValid();
        Mage::reset();
        if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE']) && $_SERVER['MAGE_IS_DEVELOPER_MODE']) {
            Mage::setIsDeveloperMode(true);
        }
        // Store or website code
        $this->mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

        // Run store or run website
        $this->mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
        
        // Initialize the Mage App and inject the testing request & response
        $app = $this->getApp($this->mageRunCode, $this->mageRunType, $this->options);
        $this->overrideRequest($app);
        $this->overrideResponse($app);
    }
    
    /**
     *
     *
     * @return void
     * @author Alistair Stead
     **/
    public function overrideRequest(Mage_Core_Model_App $app)
    {
        $appRelection = $this->getAppReflection();
        $_request = $appRelection->getProperty('_request');
        $_request->setAccessible(true);
        $_request->setValue($app, new Ibuildings_Mage_Controller_Request_HttpTestCase);
    }
    
    /**
     *
     *
     * @return void
     * @author Alistair Stead
     **/
    public function overrideResponse(Mage_Core_Model_App $app)
    {
        $appRelection = $this->getAppReflection();
        $_response = $appRelection->getProperty('_response');
        $_response->setAccessible(true);
        $_response->setValue($app, new Ibuildings_Mage_Controller_Response_HttpTestCase);
    }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Alistair Stead
     **/
    public function getApp($code = '', $type = 'store', $options = array())
    {
        return Mage::app($code, $type, $options);
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
     * Validate that we are able to run unit tests
     *
     * @return boolean
     * @author Alistair Stead
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

        if (!Mage::isInstalled()) {
            exit('Magento Unit Tests can be runned only on installed version');
            throw new DomainException(
                'MageTest can only function if Magento is installed'
            );
        }

        return true;
    }
}
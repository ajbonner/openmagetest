<?php
/**
 * Magento PHPUnit ControllerTestCase
 *
 * @package     Ibuildings_Mage_Test_PHPUnit
 * @copyright   Copyright (c) 2011 Ibuildings. (http://www.ibuildings.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Alistair Stead <alistair@ibuildings.com>
 * @version     $Id$
 */

/**
 * PHPUnit_Framework_Magento_TestCase
 *
 * @category    Mage_Test
 * @package     Ibuildings_Mage_Test_PHPUnit
 * @subpackage  Ibuildings_Mage_Test_PHPUnit_ControllerTestCase
 * @uses        Zend_Test_PHPUnit_ControllerTestCase
 */
abstract class Ibuildings_Mage_Test_PHPUnit_ControllerTestCase
   extends Zend_Test_PHPUnit_ControllerTestCase
{
    
    /**
     * Internal member variable that will be used to define which store will be used
     *
     * @var string
     **/
    protected $mageRunCode = '';
    
    /**
     * Internal member variabe that will be used to define if it is a store or the admin that will run
     *
     * @var string
     **/
    protected $mageRunType = 'store';
    
    /**
     * Internal member variable that will hold the additional options passed to Mage::app()
     *
     * @var array
     **/
    protected $options = array();
    
    /**
     * Internal member variable that will hold any email generated
     * during the request / response with the controller
     *
     * @var Zend_Mail
     **/
    protected $_mail;
    
    /**
     * Internal registry of the original config values.
     *
     * @var array
     **/
    protected $_originalConfigValues = array();
    
    /**
     * Internal registry of the new config values.
     *
     * @var array
     **/
    protected $_newConfigValues = array();
    
    /**
     * Internal registry of the removed config values.
     *
     * @var array
     **/
    protected $_removedConfigValues = array();
    
    /**
     * Overloading: prevent overloading to special properties
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (in_array($name, array('request', 'response', 'frontController'))) {
            require_once 'Zend/Exception.php';
            throw new Zend_Exception(sprintf('Setting %s object manually is not allowed', $name));
        }
        $this->$name = $value;
    }

    /**
     * Overloading for common properties
     *
     * Provides overloading for request, response, responseMail and frontController objects.
     *
     * @param mixed $name
     * @return void
     */
    public function __get($name)
    {
        switch ($name) {
            case 'request':
                return $this->getRequest();
            case 'response':
                return $this->getResponse();
            case 'responseMail':
                return $this->getResponseMail();
            case 'frontController':
                return $this->getFrontController();
        }

        return null;
    }
    
    /**
     * Enable the Magento cache to speed up the testing
     *
     * @return void
     * @author Alistair Stead
     */
    public static function setUpBeforeClass()
    {
        // Clear any cache to ensure we testing clean config
        self::cleanCache();
    }

    /**
     * Clear the magento cache at the end of each test class
     *
     * @return void
     * @author Alistair Stead
     */
    public static function tearDownAfterClass()
    {
    }

    /**
     * Set up Magento app
     *
     * Calls {@link mageBootstrap()} by default
     *
     * @return void
     */
    protected function setUp()
    {
        self::flushCache();
        // Boostrap Magento with testing objects
        $this->mageBootstrap();
    }
    
    /**
     * Teardown the modifications to the Mage App and Config
     *
     * @return void
     * @author Alistair Stead
     **/
    protected function tearDown()
    {
        // Reset Sessions and Cookies
        $this->resetSession();
        // Reset any database config after tests have been run
        $this->resetConfig();
        // Re-initialise the Magento config after any dynamic changes during testing
        // Mage::getConfig()->reinit();
    }

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
    public function mageBootstrap()
    {
        Mage::reset();
        if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
            Mage::setIsDeveloperMode(true);
        }
        // Store or website code
        $this->mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

        // Run store or run website
        $this->mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
        
        // Initialize the Mage App and inject the testing request & response
        Mage::app($this->mageRunCode, $this->mageRunType, $this->options);
        Mage::app()->setRequest(new Ibuildings_Mage_Controller_Request_HttpTestCase);
        Mage::app()->setResponse(new Ibuildings_Mage_Controller_Response_HttpTestCase);
    }

    /**
     * Dispatch the Mage request
     *
     * If a URL is provided, sets it as the request URI in the request object.
     * Dispatches the application request.
     *
     * @param  string|null $url
     * @return void
     */
    public function dispatch($url = null)
    {
        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);
        
        Mage::app()->run(array(
            'scope_code' => $this->mageRunCode,
            'scope_type' => $this->mageRunType,
            'options'    => $this->options,
        ));
    }

    /**
     * Reset Application state
     * 
     * Reset methos can be used between dispatch requests allowing you to
     * build user journeys through the application and make assertions
     * against the reponse at each stage.
     * 
     * Dispatch your requests, make assertions and then call reset before the 
     * next dispatch call.
     *
     * Creates new request/response objects, resets Mage and globals
     * instance, and resets the action helper broker.
     *
     * @return void
     */
    public function reset()
    {
        $_GET     = array();
        $_POST    = array();
        $this->resetRequest();
        $this->resetResponse();
        $this->resetResponseMail();
        $this->mageBootstrap();
    }
    
    /**
     * Reset the browser session and cookies
     *
     * @return void
     * @author Alistair Stead
     **/
    public function resetSession()
    {
        $_SESSION = array();
        $_COOKIE = array();
    }

    /**
     * Retrieve front controller instance
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = Mage::app()->getFrontController();
        }
        
        return $this->_frontController;
    }

    /**
     * Retrieve test case request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            $this->_request = Mage::app()->getRequest();
        }
        return $this->_request;
    }

    /**
     * Retrieve test case response object
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            $this->_response = Mage::app()->getResponse();
        }
        return $this->_response;
    }
    
    /**
     * Retrieve any emails that would have been sent
     * by Magento during execution of the request
     *
     * @return Zend_Mail
     * @author Alistair Stead
     **/
    public function getResponseEmail()
    {
        if (null === $this->_mail) {
            $this->_mail = Mage::app()->getResponseEmail();
        }
        return $this->_mail;
    }
    
    /**
     * Reset the responseMail generated during the request & response
     *
     * @return void
     * @author Alistair Stead
     **/
    public function resetResponseMail()
    {
        $this->_mail = null;
    }
    
    /**
     * Reset the request object
     *
     * Useful for test cases that need to test multiple trips to the server.
     *
     * @return Zend_Test_PHPUnit_ControllerTestCase
     */
    public function resetRequest()
    {
        if ($this->request instanceof Ibuildings_Mage_Controller_Request_HttpTestCase) {
            $this->request->clearQuery()
                           ->clearPost();
        }
        $this->_request = null;
        return $this;
    }

    /**
     * Reset the response object
     *
     * Useful for test cases that need to test multiple trips to the server.
     *
     * @return Zend_Test_PHPUnit_ControllerTestCase
     */
    public function resetResponse()
    {
        $this->response->clearAllHeaders();
        $this->response->clearBody();
        $this->_resetPlaceholders();
        $this->_response = null;
        return $this;
    }
    
    /**
     * Assert that the specified route was used
     *
     * @param  string $route
     * @param  string $message
     * @return void
     */
    public function assertRoute($route, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($route != $this->getRequest()->getRequestedRouteName()) {
            $msg = sprintf('Failed asserting matched route was "%s", actual route is %s',
                $route,
                $this->getRequest()->getRequestedRouteName()
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
    
    /**
     * Assert that the route matched is NOT as specified
     *
     * @param  string $route
     * @param  string $message
     * @return void
     */
    public function assertNotRoute($route, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($route == $this->getRequest()->getRequestedRouteName()) {
            $msg = sprintf('Failed asserting route matched was NOT "%s"', $route);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
    
    /**
     * Assert that the last handled request used the given module
     *
     * @param  string $module
     * @param  string $message
     * @return void
     */
    public function assertControllerModule($module, $message = '')
    {
        $this->_incrementAssertionCount();
        if ($module != $this->request->getControllerModule()) {
            $msg = sprintf('Failed asserting last controller module used <"%s"> was "%s"',
                $this->request->getControllerModule(),
                $module
            );
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }
    
    
    /**
     * Enable the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function enableCache($types = null)
    {
        if (is_null($types)) {
            $ypes = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        $allTypes = Mage::app()->useCache();
        $cacheTypes = array();
        foreach ($types as $type) {
            $cacheTypes[] = $type->getId();
        }

        $updatedTypes = 0;
        foreach ($cacheTypes as $code) {
            if (empty($allTypes[$code])) {
                $allTypes[$code] = 1;
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
        }
    }
    
    /**
     * Disable the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function disableCache($types = null)
    {
        if (is_null($types)) {
            $ypes = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        $allTypes = Mage::app()->useCache();
        $cacheTypes = array();
        foreach ($types as $type) {
            $cacheTypes[] = $type->getId();
        }

        $updatedTypes = 0;
        foreach ($cacheTypes as $type) {
            if (!empty($allTypes[$code])) {
                $allTypes[$code] = 0;
                $updatedTypes++;
            }
            $tags = Mage::app()->getCacheInstance()->cleanType($type);
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
        }
    }
    
    /**
     * Clear the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function cleanCache($types = null)
    {
        if (is_null($types)) {
            $types = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        
        if (!empty($types)) {
            foreach ($types as $type) {
                $tags = Mage::app()->getCacheInstance()->cleanType($type);
            }
        }
    }
    
    /**
     * Entirely flush the cache within the system
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function flushCache()
    {
        Mage::app()->getCacheInstance()->flush();
    }
    
    /**
     * Set an internal config value for Magento
     * 
     * Orginal values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @return void
     * @author Alistair Stead
     **/
    public function setConfig($path, $value, $scope = null)
    {
        $configCollection = Mage::getModel('core/config_data')->getCollection();
            
        $configCollection->addFieldToFilter('path', array("eq" => $path));
        if (is_string($scope)) {
            $configCollection->addFieldToFilter('scope', array("eq" => $scope));
        }
        $configCollection->load();
        
        // If existing config does not exist create it
        if (count($configCollection) == 0) {
            $configData = Mage::getModel('core/config_data');
            $configData->setPath($path);
            $configData->setValue($value);
            // Calculate scope
            $scope = ($scope)? $scope : 'default';
            $configData->setScope($scope);
            $configData->save();
            $this->_newConfigValues[] = $configData;
        } 
        foreach ($configCollection as $config) {
            $this->_originalConfigValues[] = $config;
            $config->setValue($value);
            $config->save();
        }
        unset(
            $configCollection,
            $configData
        );
    }
    
    /**
     * Remove an internal config value from Magento
     * 
     * This mimics the fucntionality of the admin when you set a yes|no option
     * to no. Orginal values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @return void
     * @author Alistair Stead
     **/
    public function removeConfig($path, $scope = null)
    {
        $configCollection = Mage::getModel('core/config_data')->getCollection();  
        $configCollection->addFieldToFilter('path', array("eq" => $path));
        if (is_string($scope)) {
            $configCollection->addFieldToFilter('scope', array("eq" => $scope));
        }
        $configCollection->load();  
        foreach ($configCollection as $config) {
            $this->_removedConfigValues[] = $config;
            $config->delete();
        }
        unset($configCollection);
    }
    
    /**
     * Reset the Magento config to its original values.
     *
     * @return void
     * @author Alistair Stead
     **/
    public function resetConfig()
    {
        $config = Mage::getModel('core/config_data');
        // Reset the original values
        foreach ($this->_originalConfigValues as $value) {
            // $config->reset();
            $config->load($value->getId());
            $config->setValue($value->getValue());
            $config->save();
        }
        // Remove the new config valuse
        foreach ($this->_newConfigValues as $value) {
            // $config->reset();
            $config->load($value->getId());
            $config->delete();
        }
        // Create the values that were removed
        foreach ($this->_removedConfigValues as $value) {
            // $config->reset();
            $config->setPath($value->getPath());
            $config->setValue($value->getValue());
            // Calculate scope
            $scope = ($value->getScope())? $value->getScope() : 'default';
            $config->setScope($scope);
            $config->save();
        }
        unset(
            $config,
            $this->_originalConfigValues,
            $this->_newConfigValues,
            $this->_removedConfigValues
        );
    }
}

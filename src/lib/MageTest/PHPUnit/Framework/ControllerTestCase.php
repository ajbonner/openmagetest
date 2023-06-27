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

/** @deprecated temporary class aliases while we wait for zf1future to come up with a migration strategy for zend_test_phpunit */
class_alias(\PHPUnit\Framework\ExpectationFailedException::class, 'PHPUnit_Framework_ExpectationFailedException');
class_alias(\PHPUnit\Framework\TestCase::class, 'PHPUnit_Framework_TestCase');
class_alias(\PHPUnit\Framework\Constraint\Constraint::class, 'PHPUnit_Framework_Constraint');
class_alias(\PHPUnit\Runner\Version::class, 'PHPUnit_Runner_Version');

/**
 * MageTest_PHPUnit_Framework_ControllerTestCase
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Unit/contributors)
 */
abstract class MageTest_PHPUnit_Framework_ControllerTestCase
   extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_bootstrap;

    /**
     * Internal member variable that will be used to define which store will be used
     *
     * @var string
     */
    protected $_mageRunCode = '';

    /**
     * Internal member variabe that will be used to define if it is a store or the admin that will run
     *
     * @var string
     */
    protected $_mageRunType = 'store';

    /**
     * Internal member variable that will hold the additional options passed to Mage::app()
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Internal member variable that will hold any email generated
     * during the request / response with the controller
     *
     * @var Zend_Mail
     */
    protected $_mail;

    /**
     * Internal registry of the original config values.
     *
     * @var array
     */
    protected $_originalConfigValues = [];

    /**
     * Internal registry of the new config values.
     *
     * @var array
     */
    protected $_newConfigValues = [];

    /**
     * Internal registry of the removed config values.
     *
     * @var array
     */
    protected $_removedConfigValues = [];

    /**
     * Overloading: prevent overloading to special properties
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     * @throws Zend_Exception
     */
    public function __set($name, $value)
    {
        if (in_array($name, ['request', 'response', 'frontController'])) {
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
     * Set up Magento app
     *
     * Calls {@link mageBootstrap()} by default
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Boostrap Magento with testing objects
        $this->mageBootstrap();
    }

    /**
     * Teardown the modifications to the Mage App and Config
     *
     * @return void
     * @author Alistair Stead
     **/
    protected function tearDown(): void
    {
        // Reset Sessions and Cookies
        $this->resetSession();
        // Reset any database config after tests have been run
        $this->resetConfig();
    }

    /**
     * Bootstrap the Mage application in a similar way to the procedure
     * of index.php
     *
     * Then sets test case request and response objects in Mage_Core_App,
     * and disables returning the response.
     *
     * @return void
     */
    public function mageBootstrap()
    {
        $_SERVER['MAGE_TEST'] = true;

        $bootstrap = new MageTest_Bootstrap();
        $bootstrap->init();
    }

    /**
     * @return void
     */
    public function getBootstrap()
    {
        if (is_null($this->_bootstrap)) {
            $this->_bootstrap = new MageTest_Bootstrap();
        }

        return $this->_bootstrap;
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

        $this->getBootstrap()->app()->run(
            $this->_mageRunCode,
            $this->_mageRunType,
            $this->_options
        );
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
        $_GET     = [];
        $_POST    = [];
        $this->resetRequest();
        $this->resetResponse();
        $this->resetResponseMail();
        $this->mageBootstrap();
    }

    /**
     * Reset the browser session and cookies
     *
     * @return void
     */
    public function resetSession()
    {
        $_SESSION = [];
        $_COOKIE = [];
    }

    /**
     * Retrieve front controller instance
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = $this->getBootstrap()->app()->getFrontController();
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
            $this->_request = $this->getBootstrap()->app()->getRequest();
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
            $this->_response = $this->getBootstrap()->app()->getResponse();
        }
        return $this->_response;
    }

    /**
     * Retrieve any emails that would have been sent
     * by Magento during execution of the request
     *
     * @return Zend_Mail
     */
    public function getResponseEmail()
    {
        if (null === $this->_mail) {
            $this->_mail = $this->getBootstrap()->app()->getResponseEmail();
        }
        return $this->_mail;
    }

    /**
     * Reset the responseMail generated during the request & response
     *
     * @return void
     */
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
        if ($this->request instanceof MageTest_Controller_Request_HttpTestCase) {
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

    protected function getFormKey($route): string
    {
        $this->dispatch($route);
        $dom = new Zend_Dom_Query($this->getResponse()->getBody());
        $result = $dom->query('input[name="form_key"]');
        if (! $result->count() || ! $result->current()->getAttribute('value')) {
            throw new \RuntimeException('form_key not found with route ' . $route);
        }
        $formKey = $result->current()->getAttribute('value');
        $this->reset();

        return $formKey;
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
     * @param $types
     * @return void
     */
    public static function enableCache($types = null)
    {
        MageTest_Util_Cache::enable($types);
    }

    /**
     * Disable the Magento cache
     *
     * @param $types
     * @return void
     */
    public static function disableCache($types = null)
    {
        MageTest_Util_Cache::disable($types);
    }

    /**
     * Clear the Magento cache
     *
     * @param $types
     * @return void
     */
    public static function cleanCache($types = null)
    {
        MageTest_Util_Cache::clean($types);
    }

    /**
     * Entirely flush the cache within the system
     *
     * @return void
     */
    public static function flushCache()
    {
        MageTest_Util_Cache::flush();
    }

    /**
     * Set an internal config value for Magento
     *
     * Orginal values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @return void
     */
    public function setConfig($path, $value, $scope = null)
    {
        MageTest_Util_Config::set($path, $value, $scope = null);
    }

    /**
     * Remove an internal config value from Magento
     *
     * This mimics the fucntionality of the admin when you set a yes|no option
     * to no. Orginal values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @param string $path
     * @param string $scope
     * @return void
     */
    public function removeConfig($path, $scope = null)
    {
        $configCollection = Mage::getModel('core/config_data')->getCollection();
        $configCollection->addFieldToFilter('path', ["eq" => $path]);
        if (is_string($scope)) {
            $configCollection->addFieldToFilter('scope', ["eq" => $scope]);
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
     */
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

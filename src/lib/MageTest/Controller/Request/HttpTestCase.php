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
 * MageTest_Controller_Request_HttpTestCase
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
class MageTest_Controller_Request_HttpTestCase
    extends Mage_Core_Controller_Request_Http
{
	const XML_NODE_DIRECT_FRONT_NAMES = 'global/request/direct_front_name';

	const MAGE_TEST_USER_AGENT = 'Mage-Test Mock Browser';

    /**
     * ORIGINAL_PATH_INFO
     * @var string
     */
    protected $_originalPathInfo= '';
    protected $_storeCode       = null;
    protected $_requestString   = '';

    /**
     * Path info array used before applying rewrite from config
     *
     * @var null || array
     */
    protected $_rewritedPathInfo= null;
    protected $_requestedRouteName = null;
    protected $_routingInfo = array();

    protected $_route;

    protected $_directFrontNames = null;
    protected $_controllerModule = null;

    /**
     * Streight request flag.
     * If flag is determined no additional logic is applicable
     *
     * @var $_isStraight bool
     */
    protected $_isStraight = false;

    /**
     * Request's original information before forward.
     *
     * @var array
     */
    protected $_beforeForwardInfo = array();

    /**
     * Request headers
     * @var array
     */
    protected $_headers = [];

    /**
     * Request method
     * @var string
     */
    protected $_method = 'GET';

    /**
     * Raw POST body
     * @var string|null
     */
    protected $_rawBody;

    /**
     * Valid request method types
     * @var array
     */
    protected $_validMethodTypes = [
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
    ];

    /**
     * Clear GET values
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function clearQuery()
    {
        $_GET = [];
        return $this;
    }

    /**
     * Clear POST values
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function clearPost()
    {
        $_POST = [];
        return $this;
    }

    /**
     * Set raw POST body
     *
     * @param  string $content
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setRawBody($content)
    {
        $this->_rawBody = (string) $content;
        return $this;
    }

    /**
     * Get RAW POST body
     *
     * @return string|null
     */
    public function getRawBody()
    {
        return $this->_rawBody;
    }

    /**
     * Clear raw POST body
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function clearRawBody()
    {
        $this->_rawBody = null;
        return $this;
    }

    /**
     * Set a cookie
     *
     * @param  string $key
     * @param  mixed $value
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setCookie($key, $value)
    {
        $_COOKIE[(string) $key] = $value;
        return $this;
    }

    /**
     * Set multiple cookies at once
     *
     * @param array $cookies
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $key => $value) {
            $_COOKIE[$key] = $value;
        }
        return $this;
    }

    /**
     * Clear all cookies
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function clearCookies()
    {
        $_COOKIE = [];
        return $this;
    }

    /**
     * Set request method
     *
     * @param  string $type
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setMethod($type)
    {
        $type = strtoupper(trim((string) $type));
        if (!in_array($type, $this->_validMethodTypes)) {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid request method specified');
        }
        $this->_method = $type;
        return $this;
    }

    /**
     * Get request method
     *
     * @return string|null
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set a request header
     *
     * @param  string $key
     * @param  string $value
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setHeader($key, $value)
    {
        $key = $this->_normalizeHeaderName($key);
        $this->_headers[$key] = (string) $value;
        return $this;
    }

    /**
     * Set request headers
     *
     * @param  array $headers
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * Get request header
     *
     * @param  string $header
     * @param  mixed $default
     * @return string|null
     */
    public function getHeader($header, $default = null)
    {
        $header = $this->_normalizeHeaderName($header);
        if (array_key_exists($header, $this->_headers)) {
            return $this->_headers[$header];
        }
        return $default;
    }

    /**
     * Get all request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Clear request headers
     *
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function clearHeaders()
    {
        $this->_headers = [];
        return $this;
    }

    /**
     * Get REQUEST_URI
     *
     * @return null|string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Normalize a header name for setting and retrieval
     *
     * @param  string $name
     * @return string
     */
    protected function _normalizeHeaderName($name)
    {
        $name = strtoupper((string) $name);
        $name = str_replace('-', '_', $name);
        return $name;
    }

    /**
     * Overload the constructor to set the User_agaent header
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setHeader('User-Agent', self::MAGE_TEST_USER_AGENT);
        $this->setHeader('Accept', 'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5');
    }

    /**
     * Returns ORIGINAL_PATH_INFO.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getOriginalPathInfo()
    {
        if (empty($this->_originalPathInfo)) {
            $this->setPathInfo();
        }
        return $this->_originalPathInfo;
    }

    public function getStoreCodeFromPath()
    {
        if (!$this->_storeCode) {
            // get store view code
            if ($this->_canBeStoreCodeInUrl()) {
                $p = explode('/', trim($this->getPathInfo(), '/'));
                $storeCode = $p[0];

                $stores = Mage::app()->getStores(true, true);

                if ($storeCode !== '' && isset($stores[$storeCode])) {
                    array_shift($p);
                    $this->setPathInfo(implode('/', $p));
                    $this->_storeCode = $storeCode;
                    Mage::app()->setCurrentStore($storeCode);
                }
                else {
                    $this->_storeCode = Mage::app()->getStore()->getCode();
                }
            } else {
                $this->_storeCode = Mage::app()->getStore()->getCode();
            }

        }
        return $this->_storeCode;
    }

    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $requestUri = $this->getRequestUri();
            if (null === $requestUri) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            $pathInfo = substr($requestUri, strlen($baseUrl));

            if ((null !== $baseUrl) && (false === $pathInfo)) {
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }

            if ($this->_canBeStoreCodeInUrl()) {
                $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
                $storeCode = $pathParts[0];

                if (!$this->isDirectAccessFrontendName($storeCode)) {
                    $stores = Mage::app()->getStores(true, true);
                    if ($storeCode!=='' && isset($stores[$storeCode])) {
                        Mage::app()->setCurrentStore($storeCode);
                        $pathInfo = '/'.(isset($pathParts[1]) ? $pathParts[1] : '');
                    }
                    elseif ($storeCode !== '') {
                        $this->setActionName('noRoute');
                    }
                }
            }

            $this->_originalPathInfo = (string) $pathInfo;

            $this->_requestString = $pathInfo . ($pos!==false ? substr($requestUri, $pos) : '');
        }

        $this->_pathInfo = (string) $pathInfo;
        return $this;
    }

    /**
     * Specify new path info
     * It happen when occur rewrite based on configuration
     *
     * @param   string $pathInfo
     * @return  Mage_Core_Controller_Request_Http
     */
    public function rewritePathInfo($pathInfo)
    {
        if (($pathInfo != $this->getPathInfo()) && ($this->_rewritedPathInfo === null)) {
            $this->_rewritedPathInfo = explode('/', trim($this->getPathInfo(), '/'));
        }
        $this->setPathInfo($pathInfo);
        return $this;
    }

    /**
     * Check if can be store code as part of url
     *
     * @return bool
     */
    protected function _canBeStoreCodeInUrl()
    {
        return Mage::isInstalled() && Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL);
    }

    /**
     * Check if code declared as direct access frontend name
     * this mean what this url can be used without store code
     *
     * @param   string $code
     * @return  bool
     */
    public function isDirectAccessFrontendName($code)
    {
        $names = $this->getDirectFrontNames();
        return isset($names[$code]);
    }

    /**
     * Get list of front names available with access without store code
     *
     * @return array
     */
    public function getDirectFrontNames()
    {
        if (is_null($this->_directFrontNames)) {
            $names = Mage::getConfig()->getNode(self::XML_NODE_DIRECT_FRONT_NAMES);
            if ($names) {
                $this->_directFrontNames = $names->asArray();
            } else {
                return array();
            }
        }
        return $this->_directFrontNames;
    }

    public function getOriginalRequest()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setPathInfo($this->getOriginalPathInfo());
        return $request;
    }

    public function getRequestString()
    {
        return $this->_requestString;
    }

    public function getBasePath()
    {
        $path = parent::getBasePath();
        if (empty($path)) {
            $path = '/';
        } else {
            $path = str_replace('\\', '/', $path);
        }
        return $path;
    }

    public function getBaseUrl($raw = false)
    {
        $url = parent::getBaseUrl();
        $url = str_replace('\\', '/', $url);
        return $url;
    }

    public function setRouteName($route)
    {
        $this->_route = $route;
        $router = Mage::app()->getFrontController()->getRouterByRoute($route);
        if (!$router) return $this;
        $module = $router->getFrontNameByRoute($route);
        if ($module) {
            $this->setModuleName($module);
        }
        return $this;
    }

    public function getRouteName()
    {
        return $this->_route;
    }

    /**
     * Retrieve HTTP HOST
     *
     * @param bool $trimPort
     * @return string
     */
    public function getHttpHost($trimPort = true)
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return false;
        }
        if ($trimPort) {
            $host = explode(':', $_SERVER['HTTP_HOST']);
            return $host[0];
        }
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Set a member of the $_POST superglobal
     *
     * @param striing|array $key
     * @param mixed $value
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function setPost($key, $value = null)
    {
        if (is_array($key)) {
            $_POST = $key;
        }
        else {
            $_POST[$key] = $value;
        }
        return $this;
    }

    /**
     * Specify module name where was found currently used controller
     *
     * @param   string $module
     * @return  Mage_Core_Controller_Request_Http
     */
    public function setControllerModule($module)
    {
        $this->_controllerModule = $module;
        return $this;
    }

    /**
     * Get module name of currently used controller
     *
     * @return  string
     */
    public function getControllerModule()
    {
        return $this->_controllerModule;
    }

    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_module;
    }
    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controller;
    }
    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_action;
    }

    /**
     * Retrieve an alias
     *
     * Retrieve the actual key represented by the alias $name.
     *
     * @param string $name
     * @return string|null Returns null when no alias exists
     */
    public function getAlias($name)
    {
        $aliases = $this->getAliases();
        if (isset($aliases[$name])) {
            return $aliases[$name];
        }
        return null;
    }

    /**
     * Retrieve the list of all aliases
     *
     * @return array
     */
    public function getAliases()
    {
        if (isset($this->_routingInfo['aliases'])) {
            return $this->_routingInfo['aliases'];
        }
        return parent::getAliases();
    }

    /**
     * Get route name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedRouteName()
    {
        if (isset($this->_routingInfo['requested_route'])) {
            return $this->_routingInfo['requested_route'];
        }
        if ($this->_requestedRouteName === null) {
            if ($this->_rewritedPathInfo !== null && isset($this->_rewritedPathInfo[0])) {
                $fronName = $this->_rewritedPathInfo[0];
                $router = Mage::app()->getFrontController()->getRouterByFrontName($fronName);
                $this->_requestedRouteName = $router->getRouteByFrontName($fronName);
            } else {
                // no rewritten path found, use default route name
                return $this->getRouteName();
            }
        }
        return $this->_requestedRouteName;
    }

    /**
     * Get controller name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedControllerName()
    {
        if (isset($this->_routingInfo['requested_controller'])) {
            return $this->_routingInfo['requested_controller'];
        }
        if (($this->_rewritedPathInfo !== null) && isset($this->_rewritedPathInfo[1])) {
            return $this->_rewritedPathInfo[1];
        }
        return $this->getControllerName();
    }

    /**
     * Get action name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedActionName()
    {
        if (isset($this->_routingInfo['requested_action'])) {
            return $this->_routingInfo['requested_action'];
        }
        if (($this->_rewritedPathInfo !== null) && isset($this->_rewritedPathInfo[2])) {
            return $this->_rewritedPathInfo[2];
        }
        return $this->getActionName();
    }

    /**
     * Set routing info data
     *
     * @param array $data
     * @return Mage_Core_Controller_Request_Http
     */
    public function setRoutingInfo($data)
    {
        if (is_array($data)) {
            $this->_routingInfo = $data;
        }
        return $this;
    }

    /**
     * Collect properties changed by _forward in protected storage
     * before _forward was called first time.
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function initForward()
    {
        if (empty($this->_beforeForwardInfo)) {
            $this->_beforeForwardInfo = array(
                'params' => $this->getParams(),
                'action_name' => $this->getActionName(),
                'controller_name' => $this->getControllerName(),
                'module_name' => $this->getModuleName()
            );
        }

        return $this;
    }

    /**
     * Retrieve property's value which was before _forward call.
     * If property was not changed during _forward call null will be returned.
     * If passed name will be null whole state array will be returned.
     *
     * @param string $name
     * @return array|string|null
     */
    public function getBeforeForwardInfo($name = null)
    {
        if (is_null($name)) {
            return $this->_beforeForwardInfo;
        } elseif (isset($this->_beforeForwardInfo[$name])) {
            return $this->_beforeForwardInfo[$name];
        }

        return null;
    }

    /**
     * Specify/get _isStraight flag value
     *
     * @param bool $flag
     * @return bool
     */
    public function isStraight($flag = null)
    {
        if ($flag !== null) {
            $this->_isStraight = $flag;
        }
        return $this->_isStraight;
    }

    /**
     * Check is Request from AJAX
     *
     * @return boolean
     */
    public function isAjax()
    {
        if ($this->isXmlHttpRequest()) {
            return true;
        }
        if ($this->getParam('ajax') || $this->getParam('isAjax')) {
            return true;
        }
        return false;
    }
}

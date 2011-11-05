<?php
/**
 * Magento Controller Response HttpTestCase
 *
 * @package     MageTest_Controller_Response
 * @copyright   Copyright (c) 2011 Ibuildings. (http://www.ibuildings.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Alistair Stead <alistair@ibuildings.com>
 * @version     $Id$
 */

/**
 * MageTest_Controller_Response_HttpTestCase
 *
 * @category    MageTest
 * @package     MageTest_Controller_Response
 * @subpackage  MageTest_Controller_Response
 */
class MageTest_Controller_Response_HttpTestCase
    extends Zend_Controller_Response_HttpTestCase
{
	public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
        return parent::sendResponse();
    }
}
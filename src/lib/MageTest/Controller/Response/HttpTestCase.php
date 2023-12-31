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
 * MageTest_Controller_Response_HttpTestCase
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
class MageTest_Controller_Response_HttpTestCase
    extends Mage_Core_Controller_Response_Http
{
    /**
     * "send" headers by returning array of all headers that would be sent
     *
     * @return array
     */
    public function sendHeaders()
    {
        $headers = [];
        foreach ($this->_headersRaw as $header) {
            $headers[] = $header;
        }
        foreach ($this->_headers as $header) {
            $name = $header['name'];
            $key  = strtolower($name);
            if (array_key_exists($name, $headers)) {
                if ($header['replace']) {
                    $headers[$key] = $header['name'] . ': ' . $header['value'];
                }
            } else {
                $headers[$key] = $header['name'] . ': ' . $header['value'];
            }
        }
        return $headers;
    }

    /**
     * Can we send headers?
     *
     * @param  bool $throw
     * @return bool
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }

    /**
     * Return the concatenated body segments
     *
     * @return string
     */
    public function outputBody()
    {
        $fullContent = '';
        foreach ($this->_body as $content) {
            $fullContent .= $content;
        }
        return $fullContent;
    }

    /**
     * Get body and/or body segments
     *
     * @param  bool|string $spec
     * @return string|array|null
     */
    public function getBody($spec = false)
    {
        if (false === $spec) {
            return $this->outputBody();
        } elseif (true === $spec) {
            return $this->_body;
        } elseif (is_string($spec) && isset($this->_body[$spec])) {
            return $this->_body[$spec];
        }

        return null;
    }

    /**
     * "send" Response
     *
     * Concats all response headers, and then final body (separated by two
     * newlines)
     *
     * @return string
     */
    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', ['response'=>$this]);

        $headers = $this->sendHeaders();
        $content = implode("\n", $headers) . "\n\n";

        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            $content .= $exceptions;
        } else {
            $content .= $this->outputBody();
        }

        return $content;
    }
}

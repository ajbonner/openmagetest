<?php

class MageTest_Core_Helper_Http extends Mage_Core_Helper_Http
{

    /**
     * Send auth failed Headers and exit
     *
     */
    public function authFailed()
    {
        Mage::app()->getResponse()
            ->setHeader('HTTP/1.1','401 Unauthorized')
            ->setHeader('WWW-Authenticate','Basic realm="RSS Feeds"')
            ->setBody('<h1>401 Unauthorized</h1>')
            ->sendResponse();
        // exit;
    }
}

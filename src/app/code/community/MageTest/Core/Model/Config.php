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
 * Mage_Core_Model_Config
 *
 * @category   MageTest
 * @package    Mage-Test_MagentoExtension
 *
 * @author     MageTest team (https://github.com/MageTest/Mage-Test/contributors)
 */
class MageTest_Core_Model_Config extends Mage_Core_Model_Config
{
    /**
     * Array of mock objects, indexed by model handle
     *
     * @var array
     */
    protected $mockObject = [];

    /**
     * Array of event names that should not be dispatched
     *
     * @var string[]
     */
    protected $disabledEvents = [];

    /**
     * Flag to disable dispatch of all events
     *
     * @var bool
     */
    protected $disableAllEvents = false;

    /**
     * Array of observer names that are permitted to receive events
     */
    protected $observerWhitelist = [];

    /**
     * Array of observer names that are not permitted to receive events
     */
    protected $observerBlacklist = [];

    /**
     * Set a mock object instance for the given model class
     *
     * @param string $modelClass Using magento group name syntax e.g. catalog/product
     * @param object $mockObject
     *
     * @return void
     */
    public function setModelInstanceMock($modelClass, $mockObject): void
    {
        $this->mockObject[$modelClass][] = $mockObject;
    }

    /**
     * Reset the mock stack. Only for provided model class, if supplied
     *
     * @param string $modelClass
     *
     * @return null
     */
    public function resetMockStack($modelClass = null)
    {
        if (is_null($modelClass)) {
            $this->mockObject = [];
        } else {
            unset($this->mockObject[$modelClass]);
        }
    }

    public function getEventConfig($area, $eventName)
    {
        if ($this->disableAllEvents) {
            return false;
        }

        $config = parent::getEventConfig($area, $eventName);
        $observersToRemove = [];

        if ($config && ! empty($this->observerWhitelist)) {
            foreach ($config->observers->children() as $observerName => $observerConfig) {
                if (! in_array($observerName, $this->observerWhitelist)) {
                    $observersToRemove[$observerName] = $observerConfig;
                }
            }
        }

        if ($config && ! empty($this->observerBlacklist)) {
            foreach ($config->observers->children() as $observerName => $observerConfig) {
                if (in_array($observerName, $this->observerBlacklist)) {
                    $observersToRemove[$observerName] = $observerConfig;
                }
            }
        }

        // This is done so laboriously as SimpleXMLElement goes haywire if you loop over its children
        // and remove any of them at the same time... so do that after working out which ones have to go.
        foreach ($observersToRemove as $observer) {
            $this->removeObserverFromEventConfig($observer);
        }

        return $config;
    }

    /**
     * @param SimpleXmlElement $observerConfig
     * @return $this
     */
    protected function removeObserverFromEventConfig($observerConfig)
    {
        $dom = dom_import_simplexml($observerConfig);
        $dom->parentNode->removeChild($dom);
        return $this;
    }

    /**
     * Override of getModelInstance that will check if a mock object has been provided.
     * Mock objects are returned in a queue, until the last object, which will always be returned thereafter.
     *
     * @param string $modelClass
     * @param array  $constructArguments
     *
     * @return object
     */
    public function getModelInstance($modelClass = '', $constructArguments = [])
    {
        if (isset($this->mockObject[$modelClass])) {
            if (count($this->mockObject[$modelClass]) > 1) {
                return array_shift($this->mockObject[$modelClass]);
            } else {
                return $this->mockObject[$modelClass][0];
            }
        }
        return parent::getModelInstance($modelClass, $constructArguments);
    }

    /**
     * @param bool $disableAllEvents
     * @return $this
     */
    public function disableAllEvents($disableAllEvents)
    {
        $this->disableAllEvents = $disableAllEvents;

        return $this;
    }

    /**
     * @param string $observerName
     * @return $this
     */
    public function whitelistObserver($observerName)
    {
        $this->observerWhitelist[] = $observerName;

        if (in_array($observerName, $this->observerBlacklist)) {
            array_filter($this->observerBlacklist, function($blacklistedObserver) use ($observerName) {
                return $blacklistedObserver != $observerName;
            });
        }

        return $this;
    }

    /**
     * @param $observerName
     * @return $this
     */
    public function blacklistObserver($observerName)
    {
        $this->observerBlacklist[] = $observerName;

        return $this;
    }

    /**
     * Add event with name $event to a list of events that should not be dispatched
     *
     * @param string $event
     * @return $this
     */
    public function disableEvent($event)
    {
        $this->disabledEvents[] = $event;
        return $this;
    }

    /**
     * Remove event with name $event from list of events that should not be dispatched

     * @param string $event
     * @return $this
     */
    public function reenableEvent($event)
    {
        if (in_array($event, $this->disabledEvents)) {
            $this->disabledEvents = array_diff($this->disabledEvents, [$event]);
        }

        return $this;
    }

    /**
     * Return a list of event names that should not be dispatched
     *
     * @return string[]
     */
    public function getDisabledEvents()
    {
        return $this->disabledEvents;
    }
}

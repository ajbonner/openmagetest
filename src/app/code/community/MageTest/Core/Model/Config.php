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
    protected $mockObject = array();

    /**
     * Array of event names that should not be dispatched
     *
     * @var string[]
     */
    protected $disabledEvents = array();

    /**
     * Set a mock object instance for the given model class
     *
     * @param string $modelClass
     * @param object $mockObject
     *
     * @return null
     */
    public function setModelInstanceMock($modelClass, $mockObject)
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
            $this->mockObject = array();
        } else {
            unset($this->mockObject[$modelClass]);
        }
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
    public function getModelInstance($modelClass = '', $constructArguments = array())
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
            $this->disabledEvents = array_diff($this->disabledEvents, array($event));
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

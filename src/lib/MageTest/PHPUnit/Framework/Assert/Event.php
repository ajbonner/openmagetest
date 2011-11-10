<?php

/**
* 
*/
class MageTest_PHPUnit_Framework_Assert_Event extends PHPUnit_Framework_Assert
{
    
    /**
     * Asserts that event was dispatched at least once
     *
     * @param string|array $event
     * @param string $message
     */
    public static function assertEventDispatched($eventName)
    {
        if (is_array($eventName)) {
            foreach ($eventNames as $eventName) {
                self::assertEventDispatched($eventName);
            }
            return;
        }

        $actual = Mage::app()->getDispatchedEventCount($eventName);
        $message = sprintf('%s event was not dispatched', $eventName);
        self::assertGreaterThanOrEqual(1, $actual, $message);
    }

    /**
     * Asserts that event was not dispatched
     *
     * @param string|array $event
     * @param string $message
     */
    public static function assertEventNotDispatched($eventName)
    {
        if (is_array($eventName)) {
            foreach ($eventNames as $eventName) {
                self::assertEventNotDispatched($eventName);
            }
            return;
        }

        $actual = Mage::app()->getDispatchedEventCount($eventName);
        $message = sprintf('%s event was dispatched', $eventName);
        self::assertEquals(0, $actual, $message);
    }

    /**
     * Assert that event was dispatched exactly $times
     *
     * @param string $eventName
     * @param int
     */
    public static function assertEventDispatchedExactly($eventName, $times)
    {
        $actual = Mage::app()->getDispatchedEventCount($eventName);
        $message = sprintf(
            '%s event was dispatched only %d times, but expected to be dispatched %d times',
            $eventName, $actual, $times
        );

        self::assertEquals($times, $actual, $message);
    }
}

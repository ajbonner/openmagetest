# MageTest #

This module provides a patched version of Mage_Core enabling you to inject testing dependencies at run time. Due to the functionality of the Varien_Autoloader the local code pool is prioritised over the core. Meaning that any code duplicated from the Mage vendor namespace into the local code pool will be used over the core.

This allows you to build and run functional controller tests in the same way you would with a standard Zend Framework Application using [Zend Test](http://framework.zend.com/manual/en/zend.test.phpunit.html). This mocks the Request and Response objects to that you can query the Response within a suite of tests.

## Requirements ##

* PHPUnit 3.5+

## Install

## Usage

## Feature Request

If you have an idea of how to make this a better project or add functionality that would be of use the community then please [submit a feature request](https://github.com/alistairstead/Mage-Test/issues). Create a new ticket and add the label of 'Feature'.

## Contributing

Developer IRC channel for MageTest is [#magetest](irc://irc.freenode.net/magetest) on Freenode.

1. [Fork it](https://github.com/alistairstead/Mage-Test/fork_select)
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Add tests for your new feature of bug fix.
4. Add your Feature or Fix to satisfy the tests.
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create new Pull Request


OpenMageTest
========

OpenMageTest is a (partially) modernized port of [Mage-Test](https://github.com/MageTest/Mage-Test) 
This module provides a patched version of Mage_Core enabling you to inject testing dependencies at run time. Due to the functionality of the Varien_Autoloader the local code pool is prioritised over the core. Meaning that any code duplicated from the Mage vendor namespace into the local code pool will be used over the core.

This allows you to build and run functional controller tests in the same way you would with a standard Zend Framework Application using [Zend Test](https://framework.zend.com/manual/1.12/en/zend.test.html). This mocks the Request and Response objects to that you can query the Response within a suite of tests.

With the [Zend Framework 1.x](https://framework.zend.com/manual/1.12/en/manual.html) no longer maintained, OpenMage bundles a modernized replacement [shardj/zf1-future](https://github.com/shardj/zf1-future). OpenMageTest assumes the presence of this particular fork of Zend Framework.

## Requirements

* PHPUnit 9.0+
* PHP 8.1+
* OpenMage 20.x+

## Installation

### Install using composer

The simplest way to install OpenMageTest is to use magento-composer-installer. First, add OpenMageTest to the list of dependencies inside your store's `composer.json`, or create this file if it doesn't exist. You will also need to add some setup for magento-composer-installer, if you are not using it already. By convention this guide assumes this file exists at the root of your store's directory structure.

Inside your project's composer.json ensure your have at least the following contents.

```json
{
    "require-dev": {
        "openmagetest/magento-phpunit-extension": "*"
    },
    "repositories": [
	    {
	        "type": "composer",
	        "url": "https://github.com/ajbonner/openmagetest"
	    }
    ],
    "extra":{
        "magento-root-dir": "./"
    },
    "config": {
        "bin-dir": "shell"
    },
    "autoload": {
        "psr-0": {
            "": [
                "app",
                "app/code/local",
                "app/code/community",
                "app/code/core",
                "lib"
            ]
        }
    },
    "minimum-stability": "dev"
}
```

Now to install everything, simply let composer do its job.

```bash
$ composer install --dev
```

Afterward your OpenMage project's directory structure should look something like the following.

	./
	├── LICENSE.html
	├── LICENSE.txt
	├── LICENSE_AFL.txt
	├── RELEASE_NOTES.txt
	├── api.php
	├── app
	├── composer.json
	├── composer.lock
	├── cron.php
	├── cron.sh
	├── dev
	├── downloader
	├── errors
	├── favicon.ico
	├── get.php
	├── includes
	├── index.php
	├── index.php.sample
	├── install.php
	├── js
	├── lib
	├── mage
	├── media
	├── php.ini.sample
	├── pkginfo
	├── shell
	├── skin
	├── tests
	├── var
	└── vendor

You can verify the installation by running OpenMageTest's own bundled test suite.

```bash
$ phpunit -c vendor/ajbonner/openmagetest/tests/phpunit.xml.dist
```

You can read more about Composer on its [official webpage](http://getcomposer.org). To find out more about Magento Composer Installer see its [Github project page](https://github.com/magento-hackathon/magento-composer-installer), or take a look at [Vinai Kopp's](http://twitter.com/VinaiKopp) [MageBase](http://www.magebase.com) [Composer with Magento article](http://magebase.com/magento-tutorials/composer-with-magento/).

### Install using Modman
OpenMageTest comes bundled with a [modman](https://github.com/colinmollenhour/modman) mapping file. To install with Modman, if you haven't already, run `modman init` followed by `modman clone https://github.com/ajbonner/openmagetest

Modman maintains a detailed [wiki](https://github.com/colinmollenhour/modman/wiki/) with a Modman tutorial and further information on its use. 

## Usage

## Feature Requests

If you have an idea of how to make this a better project or add functionality that would be of use the community then please [submit a feature request](https://github.com/MageTest/Mage-Test/issues). Create a new ticket and add the label of 'Feature'.

## Contributing

1. [Fork it](https://github.com/ajbonner/openmagetest/fork_select)
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Add tests for your new feature or bug fix.
4. Add your Feature or Fix to satisfy the tests.
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create a new Pull Request


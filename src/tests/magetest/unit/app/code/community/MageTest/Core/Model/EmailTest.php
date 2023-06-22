<?php

namespace app\code\community\MageTest\Core\Model;

use Mage;
use Mage_Core_Model_Email;
use PHPUnit\Framework\TestCase;
use Zend_Mail;

class EmailTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Bootstrap Mage in the same way as during testing
        $stub = $this->getMockForAbstractClass('MageTest_PHPUnit_Framework_ControllerTestCase');
        $stub->mageBootstrap();
    }

    public function testEmailModelShouldBeReturned()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_Email',
            Mage::getModel('core/email'),
            "MageTest_Core_Model_Email was not returned as expected"
        );
    }

    public function testEmailsAreCaughtAndStoredInAppModelForInspection()
    {
        $mailer = Mage::getModel('core/email');
        $message = 'Hello, world!';

        $mail = $this->sendEmailMessage($mailer, $message);

        $this->assertEquals($message, $mail->getBodyText(true));
    }

    /**
     * @param Mage_Core_Model_Email $mailer
     * @param string $message
     * @return Zend_Mail
     */
    protected function sendEmailMessage($mailer, $message)
    {
        $mailer->setBody($message)
            ->setType('text')
            ->setToEmail('foo@bar.com')
            ->setToName('Foo Bar');

        $mailer->send();

        return Mage::app()->getResponseEmail();
    }
}

class_alias(EmailTest::class, 'MageTest_Core_Model_EmailTest', false);

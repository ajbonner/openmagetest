<?php

namespace app\code\community\MageTest\Core\Model\Email;

use Mage;
use Mage_Core_Model_Email_Template;
use PHPUnit\Framework\TestCase;
use Zend_Mail;

class MageTest_Core_Model_Email_TemplateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Bootstrap Mage in the same way as during testing
        $stub = $this->getMockForAbstractClass('MageTest_PHPUnit_Framework_ControllerTestCase');
        $stub->mageBootstrap();
    }

    public function testEmailTemplateModelShouldBeReturned()
    {
        $this->assertInstanceOf(
            'MageTest_Core_Model_Email_Template',
            Mage::getModel('core/email_template'),
            "MageTest_Core_Model_Email_Template was not returned as expected"
        );
    }

    public function testTemplateEmailsAreCaughtAndStoredInAppModelForInspection()
    {
        $mailer = Mage::getModel('core/email_template');
        $message = 'Hello, world!';

        $mail = $this->sendTemplateEmailMessage($mailer, $message);
        $this->assertEquals($message, $mail->getBodyText(true));
    }

    /**
     * @param Mage_Core_Model_Email_Template $mailer
     * @param string $message
     * @return Zend_Mail
     */
    protected function sendTemplateEmailMessage($mailer, $message)
    {
        $mailer->setSenderName('Mage Test')
            ->setTemplateText($message)
            ->setSenderEmail('baz@qux.co.uk')
            ->setTemplateSubject('Testing 123')
            ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_TEXT)
            ->send('foo@bar.com', 'Foo Bar', array());

        $mail = Mage::app()->getResponseEmail();

        return $mail;
    }
}

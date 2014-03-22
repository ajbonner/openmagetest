<?php

class MageTest_Core_Model_Email extends Mage_Core_Model_Email
{
    public function send()
    {
        if (isset($_SERVER['MAGE_TEST'])) {
            $mail = new Zend_Mail();

            if (strtolower($this->getType()) == 'html') {
                $mail->setBodyHtml($this->getBody());
            }
            else {
                $mail->setBodyText($this->getBody());
            }

            $mail->setFrom($this->getFromEmail(), $this->getFromName())
                ->addTo($this->getToEmail(), $this->getToName())
                ->setSubject($this->getSubject());
            Mage::app()->setResponseEmail($mail);
        } else {
            parent::send();
        }
    }
}
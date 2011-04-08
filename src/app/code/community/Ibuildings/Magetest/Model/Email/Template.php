<?php
/**
 * 
 */

/**
 * 
 */
class Ibuildings_MageTest_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Send mail to recipient
     *
     * @param   string      $email		  E-mail
     * @param   string|null $name         receiver name
     * @param   array       $variables    template variables
     * @return  boolean
     **/
    public function send($email, $name = null, array $variables = array())
    {
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }

        $variables['email'] = $email;
        $variables['name'] = $name;

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null) {
            $mail->setReturnPath($returnPathEmail);
        }

        if (is_array($email)) {
            foreach ($email as $emailOne) {
                $mail->addTo($emailOne, $name);
            }
        } else {
            $mail->addTo($email, '=?utf-8?B?'.base64_encode($name).'?=');
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);

        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject('=?utf-8?B?'.base64_encode($this->getProcessedTemplateSubject($variables)).'?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());

        try {
            if (isset($_SERVER['MAGE_TEST'])) {
                // Store the constructed mail in the application for testing
                Mage::app()->setResponseEmail($mail);
                return true;
            }
            $mail->send();
            $this->_mail = null;
        }
        catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);
            return false;
        }

        return true;
    }
}

<?php

/**
 * @see Mage_Adminhtml_ControllerTestCase
 */
require_once 'ControllerTestCase.php';

/**
 * Mage_Adminhtml_IndexControllerTest
 *
 * @package    Mage_Adminhtml
 * @subpackage Mage_Adminhtml_Test
 * @runTestsInSeparateProcesses
 */
class Mage_Adminhtml_IndexControllerTest extends Mage_Adminhtml_ControllerTestCase
{
    /**
     * @test
     */
    public function theAdminRouteAccessesTheAdminApplicationArea()
    {
        $this->dispatch('admin/');

        $this->assertRoute('adminhtml', "The expected route has not been matched");
        $this->assertAction('login', "The login form should be presented");
        $this->assertController('index', "The expected controller is not been used");
    }

    /**
     * @group login
     * @test
     */
    public function theIndexActionDisplaysLoginForm()
    {
        $this->dispatch('admin/index/');

        $this->assertQueryCount('form#loginForm', 1);
    }

    /**
     * @group login
     * @test
     */
    public function submittingInvalidCredsShouldDisplayError()
    {
        $this->login('invalid', 'invalid');

        $this->assertQueryCount('li.error-msg', 1);
        $this->assertQueryContentContains('li.error-msg', 'Invalid Username or Password.');
    }

    /**
     * @group login
     * @test
     */
    public function submittingValidCredsShouldDisplayDashboard()
    {
        self::cleanCache(['block_html']);
        $this->login();

        $this->assertRedirect('We should be redirected after login');
        $this->assertRedirectRegex("/^.*dashboard.*$/", 'We are not directed to the dashboard');
    }


    /**
     * @group password
     * @test
     */
    public function theForgotPasswordActionShouldDisplayForm()
    {
        $this->dispatch('admin/index/forgotpassword');

        // The forgot password form is the same as the login
        $this->assertQueryCount('form#loginForm', 1);
        $this->assertQueryCount('div.forgot-password', 1);
        $this->assertQueryContentContains('h2', 'Forgot your user name or password?');
    }

    /**
     * @group password
     * @test
     */
    public function submittingForgotPasswordWithInvalidEmailReturnsError()
    {
        $this->request->setMethod('POST')
            ->setPost(['email' => 'invalid',
                'form_key' => $this->getFormKey('admin/index/forgotpassword')
            ]);

        $this->dispatch('admin/index/forgotpassword/');

        $this->assertQueryCount('li.error-msg', 1);
        $this->assertQueryContentContains('li.error-msg', 'Invalid email address.');
    }

    /**
     * @group password
     * @test
     */
    public function submittingForgotPasswordWithValidEmailReturnsSuccess()
    {
        $this->request->setMethod('POST')
            ->setPost([
                'email' => self::$email,
                'form_key' => $this->getFormKey('admin/index/forgotpassword')
            ]);

        $this->dispatch('admin/index/forgotpassword/');

        $this->assertRedirect('admin/index/login/');
    }
}

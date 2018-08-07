<?php
declare(strict_types=1);

/**
 * File:AuthenticationTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Plugin\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\LegacyAuthenticationInterface;
use LizardMedia\PasswordMigrator\Api\PasswordManagementInterface;
use LizardMedia\PasswordMigrator\Plugin\Model\Authentication;
use Magento\Customer\Model\Authentication as AuthenticationModel;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Url;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class AuthenticationTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Plugin\Model
 */
class AuthenticationTest extends TestCase
{
    /**
     * @var MockObject|LegacyAuthenticationInterface
     */
    private $legacyAuthentication;

    /**
     * @var MockObject|PasswordManagementInterface
     */
    private $passwordManagement;

    /**
     * @var MockObject|AuthenticationModel
     */
    private $authenticationModel;

    /**
     * @var MockObject|Session
     */
    private $session;

    /**
     * @var MockObject|Url
     */
    private $url;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->legacyAuthentication = $this->getMockBuilder(LegacyAuthenticationInterface::class)
            ->getMock();
        $this->passwordManagement = $this->getMockBuilder(PasswordManagementInterface::class)
            ->getMock();
        $this->authenticationModel = $this->getMockBuilder(AuthenticationModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->url = $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws Exception
     */
    public function testAroundAuthenticateWhenCanLegacyAuthenticate()
    {
        $authenticationPlugin = new Authentication(
            $this->legacyAuthentication,
            $this->passwordManagement,
            $this->session,
            $this->url
        );
        $customerId = 3;
        $password = 'password';

        $proceed = function ($customerId, $password) {
            throw new Exception();
        };

        $this->legacyAuthentication->expects($this->once())
            ->method('canLegacyAuthenticate')
            ->with($customerId, $password)
            ->willReturn(true);
        $this->passwordManagement->expects($this->once())
            ->method('updateCustomerPassword')
            ->with($customerId, $password);

        $this->expectException(Exception::class);

        $authenticationPlugin->aroundAuthenticate(
            $this->authenticationModel,
            $proceed,
            $customerId,
            $password
        );
    }

    /**
     * @throws Exception
     */
    public function testAroundAuthenticateWhenCannotLegacyAuthenticate()
    {
        $authenticationPlugin = new Authentication(
            $this->legacyAuthentication,
            $this->passwordManagement,
            $this->session,
            $this->url
        );
        $customerId = 3;
        $password = 'password';

        $proceed = function ($customerId, $password) {
            throw new Exception();
        };

        $this->legacyAuthentication->expects($this->once())
            ->method('canLegacyAuthenticate')
            ->with($customerId, $password)
            ->willReturn(false);

        $this->expectException(Exception::class);

        $authenticationPlugin->aroundAuthenticate(
            $this->authenticationModel,
            $proceed,
            $customerId,
            $password
        );
    }


    /**
     * @throws Exception
     */
    public function testAroundAuthenticateWhenNewPasswordCantBeGenerated()
    {
        $authenticationPlugin = new Authentication(
            $this->legacyAuthentication,
            $this->passwordManagement,
            $this->session,
            $this->url
        );
        $customerId = 3;
        $password = 'password';

        $proceed = function ($customerId, $password) {
            throw new Exception();
        };

        $this->legacyAuthentication->expects($this->once())
            ->method('canLegacyAuthenticate')
            ->with($customerId, $password)
            ->willReturn(true);
        $this->passwordManagement->expects($this->once())
            ->method('updateCustomerPassword')
            ->with($customerId, $password)
            ->willThrowException(new InputException());

        $this->url->expects($this->once())
            ->method('getUrl')
            ->with('customer/account/forgotpassword')
            ->willReturn('http://test.com/customer/account/forgotpassword');

        $this->session->expects($this->once())
            ->method('setBeforeAuthUrl')
            ->with('http://test.com/customer/account/forgotpassword')
            ->willReturn($this->session);

        $this->expectException(Exception::class);

        $authenticationPlugin->aroundAuthenticate(
            $this->authenticationModel,
            $proceed,
            $customerId,
            $password
        );
    }
}

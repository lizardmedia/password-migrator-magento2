<?php

declare(strict_types=1);

/**
 * File: AccountManagementTest.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Plugin\Model;

use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Plugin\Model\AccountManagement;
use Magento\Customer\Model\AccountManagement as MagentoAccountManagement;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class AccountManagementTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Plugin\Model
 */
class AccountManagementTest extends TestCase
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $rpToken;

    /**
     * @var callable
     */
    private $proceedSucceed;

    /**
     * @var MockObject | MagentoAccountManagement
     */
    private $subject;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @var MockObject | PasswordInterface
     */
    private $password;

    /**
     * @var MockObject | PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var AccountManagement
     */
    private $accountManagement;

    /**
     * @var MockObject | Customer
     */
    private $customer;

    /**
     * @var MockObject | CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var MockObject | LoggerInterface
     */
    private $logger;

    /**
     * @retrun void
     */
    protected function setUp() : void
    {
        //Internal mocks
        $this->email = 'test@gmail.com';
        $this->rpToken = 'ec972c4cffb21803d86';
        $this->newPassword = 'test';

        $email = $this->email;
        $rpToken = $this->rpToken;
        $newPassword = $this->newPassword;
        $this->proceedSucceed = function ($email, $rpToken, $newPassword) {
            return true;
        };

        $this->password = $this->getMockBuilder(PasswordInterface::class)->getMock();

        $this->subject = $this->getMockBuilder(MagentoAccountManagement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->passwordRepository = $this->getMockBuilder(PasswordRepositoryInterface::class)
            ->getMock();
        $this->customerRegistry = $this->getMockBuilder(CustomerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->accountManagement = new AccountManagement(
            $this->passwordRepository,
            $this->customerRegistry,
            $this->logger
        );
    }


    /**
     * @test
     */
    public function testAroundResetPasswordWhenCallableThrowsException()
    {
        $email = $this->email;
        $rpToken = $this->rpToken;
        $newPassword = $this->newPassword;

        $proceed = function ($email, $rpToken, $newPassword) {
            throw new LocalizedException(__());
        };

        $this->expectException(LocalizedException::class);
        $this->accountManagement->aroundResetPassword(
            $this->subject,
            $proceed,
            $this->email,
            $this->rpToken,
            $this->newPassword
        );
    }

    /**
     * @test
     */
    public function testAroundResetPasswordWhenCustomerNotFound()
    {
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with($this->email)
            ->willThrowException(new NoSuchEntityException());

        $this->passwordRepository->expects($this->never())->method('getByCustomerId');

        $this->accountManagement->aroundResetPassword(
            $this->subject,
            $this->proceedSucceed,
            $this->email,
            $this->rpToken,
            $this->newPassword
        );
    }


    /**
     * @test
     */
    public function testAroundResetPasswordWhenLegacyPasswordNotFound()
    {
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with($this->email)
            ->willReturn($this->customer);

        $this->customer->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with(10)
            ->willThrowException(new NoSuchEntityException());

        $this->accountManagement->aroundResetPassword(
            $this->subject,
            $this->proceedSucceed,
            $this->email,
            $this->rpToken,
            $this->newPassword
        );
    }

    /**
     * @test
     */
    public function testAroundResetPasswordWhenLegacyPasswordCouldNotBeRemoved()
    {
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with($this->email)
            ->willReturn($this->customer);

        $this->customer->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with(10)
            ->willReturn($this->password);

        $this->passwordRepository->expects($this->once())
            ->method('delete')
            ->with($this->password)
            ->willThrowException(new \Exception());

        $this->logger->expects($this->once())->method('error');

        $this->accountManagement->aroundResetPassword(
            $this->subject,
            $this->proceedSucceed,
            $this->email,
            $this->rpToken,
            $this->newPassword
        );
    }


    /**
     * @test
     */
    public function testAroundResetPasswordWhenLegacyPasswordRemoved()
    {
        $this->customerRegistry->expects($this->once())
            ->method('retrieveByEmail')
            ->with($this->email)
            ->willReturn($this->customer);

        $this->customer->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with(10)
            ->willReturn($this->password);

        $this->passwordRepository->expects($this->once())
            ->method('delete')
            ->with($this->password);

        $this->accountManagement->aroundResetPassword(
            $this->subject,
            $this->proceedSucceed,
            $this->email,
            $this->rpToken,
            $this->newPassword
        );
    }
}
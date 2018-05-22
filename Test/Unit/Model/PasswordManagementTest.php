<?php
declare(strict_types=1);

/**
 * File:PasswordManagementTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Model\PasswordManagement;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;

/**
 * Class PasswordManagementTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model
 */
class PasswordManagementTest extends TestCase
{
    /**
     * @var MockObject|PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var MockObject|AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var MockObject|CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var MockObject|CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var PasswordManagement
     */
    private $passwordManagement;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->passwordRepository = $this->getMockBuilder(PasswordRepositoryInterface::class)
            ->getMock();
        $this->accountManagement = $this->getMockBuilder(AccountManagementInterface::class)
            ->getMock();
        $this->customerRepository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMock();
        $this->customerRegistry = $this->getMockBuilder(CustomerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordManagement = new PasswordManagement(
            $this->passwordRepository,
            $this->accountManagement,
            $this->customerRepository,
            $this->customerRegistry
        );
    }

    /**
     * @test
     */
    public function getUpdateCustomerPasswordSuccessfully()
    {
        $customerId = 1;
        $password = 'password';
        $email = 'test@test.com';
        $rpToken = 'bdsbds986y739h2gevw2ef';
        $customerData = $this->getMockBuilder(CustomerInterface::class)
            ->getMock();

        $this->customerRepository->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerData);
        $customerData->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);

        $this->accountManagement->expects($this->once())
            ->method('initiatePasswordReset')
            ->with($email, false)
            ->willThrowException(new Exception());

        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerRegistry->expects($this->once())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customer);
        $customer->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive(['email'], ['rp_token'])
            ->willReturnOnConsecutiveCalls($email, $rpToken);

        $this->accountManagement->expects($this->once())
            ->method('resetPassword')
            ->with($email, $rpToken, $password);

        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willReturn($passwordDTO);
        $this->passwordRepository->expects($this->once())
            ->method('delete')
            ->with($passwordDTO);

        $this->passwordManagement->updateCustomerPassword($customerId, $password);
    }

    /**
     * @test
     */
    public function getUpdateCustomerPasswordWithException()
    {
        $customerId = 1;
        $password = 'password';
        $email = 'test@test.com';
        $rpToken = 'bdsbds986y739h2gevw2ef';
        $customerData = $this->getMockBuilder(CustomerInterface::class)
            ->getMock();

        $this->customerRepository->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerData);
        $customerData->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);

        $this->accountManagement->expects($this->once())
            ->method('initiatePasswordReset')
            ->with($email, false)
            ->willThrowException(new Exception());

        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerRegistry->expects($this->once())
            ->method('retrieve')
            ->with($customerId)
            ->willReturn($customer);
        $customer->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive(['email'], ['rp_token'])
            ->willReturnOnConsecutiveCalls($email, $rpToken);

        $this->accountManagement->expects($this->once())
            ->method('resetPassword')
            ->with($email, $rpToken, $password);

        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willReturn($passwordDTO);
        $this->passwordRepository->expects($this->once())
            ->method('delete')
            ->with($passwordDTO)
            ->willThrowException(new Exception());

        $this->passwordManagement->updateCustomerPassword($customerId, $password);
    }
}

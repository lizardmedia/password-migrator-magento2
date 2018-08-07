<?php
declare(strict_types=1);

/**
 * File:PasswordManagement.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Api\PasswordManagementInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PasswordManagement
 * @package LizardMedia\PasswordMigrator\Model
 */
class PasswordManagement implements PasswordManagementInterface
{
    /**
     * @var PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * PasswordManagement constructor.
     * @param PasswordRepositoryInterface $passwordRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        PasswordRepositoryInterface $passwordRepository,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        CustomerRegistry $customerRegistry
    ) {
        $this->passwordRepository = $passwordRepository;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * @param int $customerId
     * @param string $newPassword
     * @return void
     * @throws InputException
     */
    public function updateCustomerPassword(int $customerId, string $newPassword): void
    {
        try {
            $this->generateNewResetToken($customerId);
            $this->updatePassword($customerId, $newPassword);
            $this->removeLegacyPassword($customerId);
        } catch (InputException $exception) {
            throw $exception;
        } catch (Exception $exception) {
        }
    }

    /**
     * @param int $customerId
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateNewResetToken(int $customerId): void
    {
        $customerData = $this->customerRepository->getById($customerId);
        try {
            $this->accountManagement->initiatePasswordReset($customerData->getEmail(), false);
        } catch (Exception $exception) {
        }
    }

    /**
     * @param int $customerId
     * @param string $newPassword
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function updatePassword(int $customerId, string $newPassword): void
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        $this->accountManagement->resetPassword(
            $customer->getData('email'),
            $customer->getData('rp_token'),
            $newPassword
        );
    }

    /**
     * @param int $customerId
     * @return void
     * @throws NoSuchEntityException
     * @throws Exception
     */
    private function removeLegacyPassword(int $customerId): void
    {
        $password = $this->passwordRepository->getByCustomerId($customerId);
        $this->passwordRepository->delete($password);
    }
}

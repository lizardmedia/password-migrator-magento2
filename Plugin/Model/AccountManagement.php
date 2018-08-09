<?php

declare(strict_types=1);

/**
 * File: AccountManagement.php
 *
 * @author Bartosz Kubicki bartosz.kubicki@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Plugin\Model;

use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use Magento\Customer\Model\AccountManagement as MagentoAccountManagement;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class AccountManagement
 * @package LizardMedia\PasswordMigrator\Plugin\Model
 */
class AccountManagement
{
    /**
     * @var PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PasswordManagement constructor.
     * @param PasswordRepositoryInterface $passwordRepository
     * @param CustomerRegistry $customerRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        PasswordRepositoryInterface $passwordRepository,
        CustomerRegistry $customerRegistry,
        LoggerInterface $logger
    ) {
        $this->passwordRepository = $passwordRepository;
        $this->customerRegistry = $customerRegistry;
        $this->logger = $logger;
    }


    /**
     * @param MagentoAccountManagement $subject
     * @param callable $proceed
     * @param $email
     * @param $resetToken
     * @param $newPassword
     * @return bool
     */
    public function aroundResetPassword(
        MagentoAccountManagement $subject,
        callable $proceed,
        $email,
        $resetToken,
        $newPassword
    ) {
        $result = $proceed($email, $resetToken, $newPassword);

        if ($result === true) {
            $this->processRemovingLegacyPassword($email);
        }

        return $result;
    }


    /**
     * @param string $email
     * @return void
     */
    private function processRemovingLegacyPassword(string $email) : void
    {
        try {
            $customer = $this->getCustomer($email);
            $this->removeLegacyPasswordIfExists((int) $customer->getId());
        } catch (NoSuchEntityException $exception) {
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param string $email
     * @return Customer
     * @throws NoSuchEntityException
     */
    private function getCustomer(string $email) : Customer
    {
        return $customer = $this->customerRegistry->retrieveByEmail($email);
    }


    /**
     * @param int $customerId
     * @return void
     */
    private function removeLegacyPasswordIfExists(int $customerId): void
    {
        $password = $this->passwordRepository->getByCustomerId($customerId);
        $this->passwordRepository->delete($password);
    }
}
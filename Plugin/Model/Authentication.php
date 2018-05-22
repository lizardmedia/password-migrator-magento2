<?php
declare(strict_types=1);

/**
 * File:Authentication.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Plugin\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\LegacyAuthenticationInterface;
use LizardMedia\PasswordMigrator\Api\PasswordManagementInterface;
use Magento\Customer\Model\Authentication as AuthenticationModel;

/**
 * Class Authentication
 * @package LizardMedia\PasswordMigrator\Plugin\Model
 */
class Authentication
{
    /**
     * @var LegacyAuthenticationInterface
     */
    private $legacyAuthentication;

    /**
     * @var PasswordManagementInterface
     */
    private $passwordManagement;

    /**
     * Authentication constructor.
     * @param LegacyAuthenticationInterface $legacyAuthentication
     * @param PasswordManagementInterface $passwordManagement
     */
    public function __construct(
        LegacyAuthenticationInterface $legacyAuthentication,
        PasswordManagementInterface $passwordManagement
    ) {
        $this->legacyAuthentication = $legacyAuthentication;
        $this->passwordManagement = $passwordManagement;
    }

    /**
     * @param AuthenticationModel $subject
     * @param callable $proceed
     * @param $customerId
     * @param $password
     * @return bool
     * @throws Exception
     */
    public function aroundAuthenticate(AuthenticationModel $subject, callable $proceed, $customerId, $password)
    {
        try {
            return $proceed($customerId, $password);
        } catch (Exception $e) {
            if ($this->legacyAuthentication->canLegacyAuthenticate((int)$customerId, $password)) {
                $this->passwordManagement->updateCustomerPassword((int) $customerId, $password);
                return $proceed($customerId, $password);
            } else {
                throw $e;
            }
        }
    }
}

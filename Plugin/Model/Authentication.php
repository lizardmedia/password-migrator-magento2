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
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Url;

/**
 * TODO: Unit test
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
     * @var Session
     */
    private $session;

    /**
     * @var Url
     */
    private $url;

    /**
     * Authentication constructor.
     * @param LegacyAuthenticationInterface $legacyAuthentication
     * @param PasswordManagementInterface $passwordManagement
     * @param Session $session
     * @param Url $url
     */
    public function __construct(
        LegacyAuthenticationInterface $legacyAuthentication,
        PasswordManagementInterface $passwordManagement,
        Session $session,
        Url $url
    ) {
        $this->legacyAuthentication = $legacyAuthentication;
        $this->passwordManagement = $passwordManagement;
        $this->session = $session;
        $this->url = $url;
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
                $this->updateCustomerPassword((int) $customerId, $password);
                return $proceed($customerId, $password);
            } else {
                throw $e;
            }
        }
    }


    /**
     * @param int $customerId
     * @param string $password
     * @return void
     * @throws InputException
     */
    private function updateCustomerPassword(int $customerId, string $password) : void
    {
        try {
            $this->passwordManagement->updateCustomerPassword($customerId, $password);
        } catch (InputException $exception) {
            $this->session->setBeforeAuthUrl($this->getResetPasswordUrl());
            throw $exception;
        }
    }


    /**
     * @return string
     */
    private function getResetPasswordUrl() : string
    {
        return $this->url->getUrl('customer/account/forgotpassword');
    }
}

<?php
declare(strict_types=1);

/**
 * File:PasswordManagementInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api;

/**
 * Interface PasswordManagementInterface
 * @package LizardMedia\PasswordMigrator\Api
 */
interface PasswordManagementInterface
{
    /**
     * @param int $customerId
     * @param string $newPassword
     * @return void
     */
    public function updateCustomerPassword(int $customerId, string $newPassword): void;
}

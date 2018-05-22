<?php
declare(strict_types=1);

/**
 * File:LegacyAuthenticationInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api;

/**
 * Interface LegacyAuthenticationInterface
 * @package LizardMedia\PasswordMigrator\Api
 */
interface LegacyAuthenticationInterface
{
    /**
     * @param int $customerId
     * @param string $password
     * @return bool
     */
    public function canLegacyAuthenticate(int $customerId, string $password): bool;
}

<?php
declare(strict_types=1);

/**
 * File:ExpiredPasswordCleanerInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api;

/**
 * Interface ExpiredPasswordCleanerInterface
 * @package LizardMedia\PasswordMigrator\Api
 */
interface ExpiredPasswordCleanerInterface
{
    /**
     * @return void
     */
    public function removeExpiredPasswords(): void;
}

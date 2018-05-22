<?php
declare(strict_types=1);

/**
 * File:LegacyEncryptorInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api;

/**
 * Interface LegacyEncryptorInterface
 * @package LizardMedia\PasswordMigrator\Api
 */
interface LegacyEncryptorInterface
{
    /**
     * @param string $password
     * @param string $oldSalt
     * @return string
     */
    public function encrypt(string $password, string $oldSalt): string;
}

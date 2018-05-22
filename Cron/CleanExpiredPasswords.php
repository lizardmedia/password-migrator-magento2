<?php
declare(strict_types=1);

/**
 * File:CleanExpiredPasswords.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Cron;

use LizardMedia\PasswordMigrator\Api\ExpiredPasswordCleanerInterface;

/**
 * Class CleanExpiredPasswords
 * @package LizardMedia\PasswordMigrator\Cron
 */
class CleanExpiredPasswords
{
    /**
     * @var ExpiredPasswordCleanerInterface
     */
    private $expiredPasswordCleaner;

    /**
     * CleanExpiredPasswords constructor.
     * @param ExpiredPasswordCleanerInterface $expiredPasswordCleaner
     */
    public function __construct(
        ExpiredPasswordCleanerInterface $expiredPasswordCleaner
    ) {
        $this->expiredPasswordCleaner = $expiredPasswordCleaner;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->expiredPasswordCleaner->removeExpiredPasswords();
    }
}

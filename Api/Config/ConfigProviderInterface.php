<?php
declare(strict_types=1);

/**
 * File:Interface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api\Config;

/**
 * Interface ConfigProviderInterface
 * @package LizardMedia\PasswordMigrator\Api\Config
 */
interface ConfigProviderInterface
{
    /**
     * @return int
     */
    public function getCleanupAfter(): int;
}

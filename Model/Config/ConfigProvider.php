<?php
declare(strict_types=1);

/**
 * File:ConfigProvider.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model\Config;

use LizardMedia\PasswordMigrator\Api\Config\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ConfigProvider
 * @package LizardMedia\PasswordMigrator\Model\Config
 */
class ConfigProvider implements ConfigProviderInterface
{
    const XML_PATH_CLEANUP_AFTER = 'password_migrator/general/cleanup';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int
     */
    public function getCleanupAfter(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_CLEANUP_AFTER);
    }
}

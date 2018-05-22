<?php
declare(strict_types=1);

/**
 * File:ExpiredPasswordCleaner.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\Config\ConfigProviderInterface;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Api\ExpiredPasswordCleanerInterface;
use LizardMedia\PasswordMigrator\Exception\CleanupDisabledException;
use LizardMedia\PasswordMigrator\Model\Config\Source\Cleanup;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ExpiredPasswordCleaner
 * @package LizardMedia\PasswordMigrator\Model
 */
class ExpiredPasswordCleaner implements ExpiredPasswordCleanerInterface
{
    /**
     * @var PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * ExpiredPasswordCleaner constructor.
     * @param PasswordRepositoryInterface $passwordRepository
     * @param ConfigProviderInterface $configProvider
     * @param DateTime $dateTime
     */
    public function __construct(
        PasswordRepositoryInterface $passwordRepository,
        ConfigProviderInterface $configProvider,
        DateTime $dateTime
    ) {
        $this->passwordRepository = $passwordRepository;
        $this->configProvider = $configProvider;
        $this->dateTime = $dateTime;
    }

    /**
     * @return void
     */
    public function removeExpiredPasswords(): void
    {
        try {
            $cleanupSinceDate = $this->getCleanupAfterDateString();
            $passwordsToRemove = $this->passwordRepository->getOlderThan($cleanupSinceDate);
            foreach ($passwordsToRemove as $password) {
                $this->passwordRepository->delete($password);
            }
        } catch (Exception $e) {
        }
    }

    /**
     * @return string
     * @throws CleanupDisabledException
     */
    private function getCleanupAfterDateString(): string
    {
        $cleanupAfter = $this->configProvider->getCleanupAfter();
        switch ($cleanupAfter) {
            case Cleanup::CLEAN_YEAR:
                $diff = '-1 year';
                break;
            case Cleanup::CLEAN_HALFYEAR:
                $diff = '-6 months';
                break;
            default:
                throw new CleanupDisabledException(__('Automatic cleanup disabled'));
        }

        return date('Y-m-d', strtotime("{$this->dateTime->date()} {$diff}"));
    }
}

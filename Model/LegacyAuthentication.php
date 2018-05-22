<?php
declare(strict_types=1);

/**
 * File:LegacyAuthentication.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model;

use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Api\LegacyAuthenticationInterface;
use LizardMedia\PasswordMigrator\Api\LegacyEncryptorInterface;

/**
 * Class LegacyAuthentication
 * @package LizardMedia\PasswordMigrator\Model
 */
class LegacyAuthentication implements LegacyAuthenticationInterface
{
    /**
     * @var PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var LegacyEncryptorInterface
     */
    private $legacyEncryptor;

    /**
     * LegacyAuthentication constructor.
     * @param PasswordRepositoryInterface $passwordRepository
     * @param LegacyEncryptorInterface $legacyEncryptor
     */
    public function __construct(
        PasswordRepositoryInterface $passwordRepository,
        LegacyEncryptorInterface $legacyEncryptor
    ) {
        $this->passwordRepository = $passwordRepository;
        $this->legacyEncryptor = $legacyEncryptor;
    }

    /**
     * @param int $customerId
     * @param string $password
     * @return bool
     */
    public function canLegacyAuthenticate(int $customerId, string $password): bool
    {
        try {
            $passwordDTO = $this->passwordRepository->getByCustomerId($customerId);
            $legacyHash = $this->legacyEncryptor->encrypt($password, $passwordDTO->getSalt());
            return $legacyHash === $passwordDTO->getPassword();
        } catch (Exception $e) {
            return false;
        }
    }
}

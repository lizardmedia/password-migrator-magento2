<?php
declare(strict_types=1);

/**
 * File:PasswordRepositoryInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api\Data;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PasswordRepositoryInterface
 * @package LizardMedia\PasswordMigrator\Api\Data
 */
interface PasswordRepositoryInterface
{
    /**
     * @param int $customerId
     * @return PasswordInterface
     * @throws NoSuchEntityException
     */
    public function getByCustomerId(int $customerId): PasswordInterface;

    /**
     * @param PasswordInterface $password
     * @return void
     * @throws Exception
     */
    public function save(PasswordInterface $password): void;

    /**
     * @param PasswordInterface $password
     * @return void
     * @throws Exception
     */
    public function delete(PasswordInterface $password): void;

    /**
     * @param string $dateTime
     * @return PasswordInterface[]
     */
    public function getOlderThan(string $dateTime): array;
}

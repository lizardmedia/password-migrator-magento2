<?php
declare(strict_types=1);

/**
 * File:PasswordInterface.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Api\Data;

/**
 * Interface PasswordInterface
 * @package LizardMedia\PasswordMigrator\Api\Data
 */
interface PasswordInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * @return string
     */
    public function getSalt(): string;

    /**
     * @param string $salt
     * @return void
     */
    public function setSalt(string $salt): void;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;
}

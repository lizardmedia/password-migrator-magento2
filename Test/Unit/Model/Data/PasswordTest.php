<?php
declare(strict_types=1);

/**
 * File:PasswordTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\Data;

use LizardMedia\PasswordMigrator\Model\Data\Password;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\Data
 */
class PasswordTest extends TestCase
{
    /**
     * @test
     */
    public function testGetProperties()
    {
        $id = 1;
        $customerId = 1;
        $password = '1dagag2t3';
        $salt = '901u3rthjn2g';
        $createdAt = '2018-05-20';

        $passwordDTO = new Password($id, $customerId);
        $passwordDTO->setPassword($password);
        $passwordDTO->setSalt($salt);
        $passwordDTO->setCreatedAt($createdAt);

        $this->assertEquals($id, $passwordDTO->getId());
        $this->assertEquals($customerId, $passwordDTO->getCustomerId());
        $this->assertEquals($password, $passwordDTO->getPassword());
        $this->assertEquals($salt, $passwordDTO->getSalt());
        $this->assertEquals($createdAt, $passwordDTO->getCreatedAt());
    }
}

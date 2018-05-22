<?php
declare(strict_types=1);

/**
 * File:LegacyAuthenticationTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model;

use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Model\LegacyAuthentication;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Api\LegacyEncryptorInterface;

/**
 * Class LegacyAuthenticationTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model
 */
class LegacyAuthenticationTest extends TestCase
{
    /**
     * @var MockObject|PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var MockObject|LegacyEncryptorInterface
     */
    private $legacyEncryptor;

    /**
     * @var LegacyAuthentication
     */
    private $legacyAuthentication;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->passwordRepository = $this->getMockBuilder(PasswordRepositoryInterface::class)
            ->getMock();
        $this->legacyEncryptor = $this->getMockBuilder(LegacyEncryptorInterface::class)
            ->getMock();

        $this->legacyAuthentication = new LegacyAuthentication(
            $this->passwordRepository,
            $this->legacyEncryptor
        );
    }

    /**
     * @test
     */
    public function testLegacyAuthenticationSuccessful()
    {
        $customerId = 1;
        $password = 'password';
        $salt = 'salt';
        $hashed = 'i3jti23';

        /** @var MockObject|PasswordInterface $passwordDTO */
        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willReturn($passwordDTO);

        $passwordDTO->expects($this->once())
            ->method('getSalt')
            ->willReturn($salt);

        $this->legacyEncryptor->expects($this->once())
            ->method('encrypt')
            ->with($password, $salt)
            ->willReturn($hashed);
        $passwordDTO->expects($this->once())
            ->method('getPassword')
            ->willReturn($hashed);

        $this->assertTrue($this->legacyAuthentication->canLegacyAuthenticate($customerId, $password));
    }

    /**
     * @test
     */
    public function testLegacyPasswordNotFound()
    {
        $customerId = 1;
        $password = 'password';

        $this->passwordRepository->expects($this->once())
            ->method('getByCustomerId')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertFalse($this->legacyAuthentication->canLegacyAuthenticate($customerId, $password));
    }
}

<?php
declare(strict_types=1);

/**
 * File:ExpiredPasswordCleanerTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model;

use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Model\ExpiredPasswordCleaner;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use LizardMedia\PasswordMigrator\Api\Config\ConfigProviderInterface;
use LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface;
use LizardMedia\PasswordMigrator\Api\ExpiredPasswordCleanerInterface;
use LizardMedia\PasswordMigrator\Exception\CleanupDisabledException;
use LizardMedia\PasswordMigrator\Model\Config\Source\Cleanup;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ExpiredPasswordCleanerTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model
 */
class ExpiredPasswordCleanerTest extends TestCase
{
    /**
     * @var MockObject|PasswordRepositoryInterface
     */
    private $passwordRepository;

    /**
     * @var MockObject|ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var MockObject|DateTime
     */
    private $dateTime;

    /**
     * @var ExpiredPasswordCleaner
     */
    private $expiredPasswordCleaner;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->passwordRepository = $this->getMockBuilder(PasswordRepositoryInterface::class)
            ->getMock();
        $this->configProvider = $this->getMockBuilder(ConfigProviderInterface::class)
            ->getMock();
        $this->dateTime = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expiredPasswordCleaner = new ExpiredPasswordCleaner(
            $this->passwordRepository,
            $this->configProvider,
            $this->dateTime
        );
    }

    /**
     * @test
     * @dataProvider removeExpiredPasswordsDataProvider
     */
    public function testRemoveExpiredPasswords($cleanupAfter, $dateTime)
    {
        $this->configProvider->expects($this->once())
            ->method('getCleanupAfter')
            ->willReturn($cleanupAfter);
        $this->dateTime->expects($this->once())
            ->method('date')
            ->willReturn('2018-05-22');

        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();

        $this->passwordRepository->expects($this->once())
            ->method('getOlderThan')
            ->with($dateTime)
            ->willReturn([$passwordDTO]);

        $this->passwordRepository->expects($this->once())
            ->method('delete')
            ->with($passwordDTO);

        $this->expiredPasswordCleaner->removeExpiredPasswords();
    }

    /**
     * @test
     */
    public function testRunCleanerWithCleanupDisabled()
    {
        $this->configProvider->expects($this->once())
            ->method('getCleanupAfter')
            ->willReturn(0);

        $this->expiredPasswordCleaner->removeExpiredPasswords();
    }

    /**
     * @return array
     */
    public function removeExpiredPasswordsDataProvider()
    {
        return [
            [
                1,
                '2017-11-22'
            ],
            [
                2,
                '2017-05-22'
            ]
        ];
    }
}

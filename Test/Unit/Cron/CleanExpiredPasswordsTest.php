<?php
declare(strict_types=1);

/**
 * File:CleanExpiredPasswordsTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Cron;

use LizardMedia\PasswordMigrator\Api\ExpiredPasswordCleanerInterface;
use LizardMedia\PasswordMigrator\Cron\CleanExpiredPasswords;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CleanExpiredPasswordsTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Cron
 */
class CleanExpiredPasswordsTest extends TestCase
{
    /**
     * @test
     */
    public function testExecuteStartsCleaningProcess()
    {
        /** @var MockObject|ExpiredPasswordCleanerInterface $expiredPasswordsCleaner */
        $expiredPasswordsCleaner = $this->getMockBuilder(ExpiredPasswordCleanerInterface::class)
            ->getMock();

        $cronJob = new CleanExpiredPasswords($expiredPasswordsCleaner);

        $expiredPasswordsCleaner->expects($this->once())
            ->method('removeExpiredPasswords');

        $cronJob->execute();
    }
}

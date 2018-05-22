<?php
declare(strict_types=1);

/**
 * File:DataProviderTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\Config;

use LizardMedia\PasswordMigrator\Model\Config\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class DataProviderTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\Config
 */
class DataProviderTest extends TestCase
{
    /**
     * @var MockObject|ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $this->configProvider = new ConfigProvider($this->scopeConfig);
    }

    /**
     * @test
     */
    public function testGetCleanupAfter()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('password_migrator/general/cleanup')
            ->willReturn('2');

        $expected = 2;
        $this->assertEquals($expected, $this->configProvider->getCleanupAfter());
    }
}

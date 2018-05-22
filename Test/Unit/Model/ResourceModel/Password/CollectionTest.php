<?php
declare(strict_types=1);

/**
 * File:CollectionTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\ResourceModel\Password;

use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password\Collection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Class CollectionTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\ResourceModel\Password
 */
class CollectionTest extends TestCase
{
    /**
     * @var MockObject|Collection
     */
    private $collection;

    /**
     * @return void
     */
    protected function setUp()
    {
        $entityFactory = $this->getMockBuilder(EntityFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $fetchStrategy = $this->getMockBuilder(FetchStrategyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resource = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resource->expects($this->any())
            ->method('getConnection')
            ->willReturn($connection);

        $this->collection = $this->getMockBuilder(Collection::class)
            ->setConstructorArgs(
                [
                    $entityFactory,
                    $logger,
                    $fetchStrategy,
                    $eventManager,
                    $connection,
                    $resource
                ]
            )
            ->setMethods(['_initSelect'])
            ->getMock();
    }

    /**
     * @test
     */
    public function testCorrectModelIsInitialized()
    {
        $expectedModel = Password::class;

        $reflection = new ReflectionClass(get_class($this->collection));
        $field = $reflection->getProperty('_model');
        $field->setAccessible(true);

        $this->assertEquals($expectedModel, $field->getValue($this->collection));
    }

    /**
     * @test
     */
    public function testCorrectResourceModelIsInitialized()
    {
        $expectedResourceModel = PasswordResource::class;

        $reflection = new ReflectionClass(get_class($this->collection));
        $field = $reflection->getProperty('_resourceModel');
        $field->setAccessible(true);

        $this->assertEquals($expectedResourceModel, $field->getValue($this->collection));
    }
}

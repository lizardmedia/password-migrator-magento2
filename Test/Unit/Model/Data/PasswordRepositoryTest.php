<?php
declare(strict_types=1);

/**
 * File:PasswordRepositoryTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\Data;

use LizardMedia\PasswordMigrator\Model\Data\PasswordRepository;
use PHPUnit\Framework\TestCase;
use Exception;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterface;
use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\PasswordFactory;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password\CollectionFactory;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password\Collection;

/**
 * Class PasswordRepositoryTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\Data
 */
class PasswordRepositoryTest extends TestCase
{
    /**
     * @var MockObject|PasswordFactory
     */
    private $passwordFactory;

    /**
     * @var MockObject|PasswordResource
     */
    private $passwordResource;

    /**
     * @var MockObject|PasswordRepository
     */
    private $passwordRepository;

    /**
     * @var MockObject|CollectionFactory
     */
    private $collectionFactory;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->passwordFactory = $this->getMockBuilder(PasswordFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->passwordResource = $this->getMockBuilder(PasswordResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordRepository = new PasswordRepository(
            $this->passwordFactory,
            $this->passwordResource,
            $this->collectionFactory
        );
    }

    /**
     * @test
     * @throws NoSuchEntityException
     */
    public function testGetByFoundCustomerId()
    {
        $password = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();
        $password->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->passwordFactory->expects($this->once())
            ->method('create')
            ->willReturn($password);
        $this->passwordResource->expects($this->once())
            ->method('load')
            ->with($password, 1, 'customer_id');
        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $password->expects($this->once())
            ->method('getDataModel')
            ->willReturn($passwordDTO);

        $this->assertInstanceOf(PasswordInterface::class, $this->passwordRepository->getByCustomerId(1));
    }

    /**
     * @test
     * @throws NoSuchEntityException
     */
    public function testGetByNotFoundCustomerId()
    {
        $password = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();
        $password->expects($this->once())
            ->method('getId')
            ->willReturn(0);
        $this->passwordFactory->expects($this->once())
            ->method('create')
            ->willReturn($password);
        $this->passwordResource->expects($this->once())
            ->method('load')
            ->with($password, 1, 'customer_id');

        $this->expectException(NoSuchEntityException::class);

        $this->passwordRepository->getByCustomerId(1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function testSave()
    {
        /** @var MockObject|PasswordInterface $passwordDTO */
        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $password = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();
        $passwordDTO->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $this->passwordFactory->expects($this->once())
            ->method('create')
            ->willReturn($password);
        $this->passwordResource->expects($this->once())
            ->method('load')
            ->with($password, 1);

        $password->expects($this->exactly(3))
            ->method('setData');

        $this->passwordResource->expects($this->once())
            ->method('save')
            ->with($password);

        $this->passwordRepository->save($passwordDTO);
    }

    /**
     * @test
     * @throws Exception
     */
    public function testDeleteExistingObject()
    {
        /** @var MockObject|PasswordInterface $passwordDTO */
        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $passwordDTO->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $password = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->passwordFactory->expects($this->once())
            ->method('create')
            ->willReturn($password);
        $this->passwordResource->expects($this->once())
            ->method('load')
            ->with($password, 1);
        $this->passwordResource->expects($this->once())
            ->method('delete')
            ->with($password);

        $this->passwordRepository->delete($passwordDTO);
    }

    /**
     * @test
     * @throws Exception
     */
    public function testDeleteNotExistingObject()
    {
        /** @var MockObject|PasswordInterface $passwordDTO */
        $passwordDTO = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $passwordDTO->expects($this->once())
            ->method('getId')
            ->willReturn(0);

        $this->expectException(NoSuchEntityException::class);

        $this->passwordRepository->delete($passwordDTO);
    }

    /**
     * @test
     */
    public function testGetOlderThan()
    {
        $dateTime = '2018-05-22';
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('created_at', ['lt' => $dateTime])
            ->willReturn($collection);
        $collection->expects($this->once())
            ->method('setPageSize')
            ->with(60000)
            ->willReturn($collection);
        $collection->expects($this->once())
            ->method('setCurPage')
            ->with(1)
            ->willReturn($collection);

        $password1 = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();
        $password2 = $this->getMockBuilder(Password::class)
            ->disableOriginalConstructor()
            ->getMock();

        $passwordDTO1 = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();
        $passwordDTO2 = $this->getMockBuilder(PasswordInterface::class)
            ->getMock();

        $password1->expects($this->once())
            ->method('getDataModel')
            ->willReturn($passwordDTO1);
        $password2->expects($this->once())
            ->method('getDataModel')
            ->willReturn($passwordDTO2);

        $collection->expects($this->once())
            ->method('getItems')
            ->willReturn([$password1, $password2]);

        $expected = [$passwordDTO1, $passwordDTO2];

        $this->assertEquals($expected, $this->passwordRepository->getOlderThan($dateTime));
    }
}

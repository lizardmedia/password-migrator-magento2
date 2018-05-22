<?php
declare(strict_types=1);

/**
 * File:PasswordTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use LizardMedia\PasswordMigrator\Api\Data\PasswordInterfaceFactory;
use LizardMedia\PasswordMigrator\Model\Data\Password as PasswordDTO;
use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PasswordTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model
 */
class PasswordTest extends TestCase
{
    /**
     * @var MockObject|PasswordInterfaceFactory
     */
    private $passwordFactory;

    /**
     * @var MockObject|Context
     */
    private $context;

    /**
     * @var MockObject|Registry
     */
    private $registry;

    /**
     * @var MockObject|PasswordResource
     */
    private $resource;

    /**
     * @var MockObject|Password
     */
    private $password;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->passwordFactory = $this->getMockBuilder(PasswordInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource = $this->getMockBuilder(PasswordResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->any())
            ->method('getIdFieldName')
            ->willReturn(PasswordResource::PRIMARY_KEY);

        $this->password = $this->getMockBuilder(Password::class)
            ->setConstructorArgs(
                [
                    $this->passwordFactory,
                    $this->context,
                    $this->registry,
                    $this->resource
                ]
            )
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     */
    public function testGetDataModel()
    {
        $this->password->setData(Password::ID, 1);
        $this->password->setData(Password::CUSTOMER_ID, 1);
        $this->password->setData(Password::PASSWORD, 'wqfwqwqgdb');
        $this->password->setData(Password::SALT, 'ugu23t24oigklr');
        $this->password->setData(Password::CREATED_AT, '01-01-2011');

        $dataObject = new PasswordDTO(
            1,
            1
        );

        $this->passwordFactory->expects($this->once())
            ->method('create')
            ->with(
                [
                    'id' => $this->password->getData(Password::ID),
                    'customerId' => $this->password->getData(Password::CUSTOMER_ID)
                ]
            )
            ->willReturn($dataObject);

        $returnedObject = $this->password->getDataModel();

        $expectedId = 1;
        $expectedCustomerId = 1;
        $expectedPassword = 'wqfwqwqgdb';
        $expectedSalt = 'ugu23t24oigklr';
        $expectedCreatedAt = '01-01-2011';

        $this->assertEquals($expectedId, $returnedObject->getId());
        $this->assertEquals($expectedCustomerId, $returnedObject->getCustomerId());
        $this->assertEquals($expectedPassword, $returnedObject->getPassword());
        $this->assertEquals($expectedSalt, $returnedObject->getSalt());
        $this->assertEquals($expectedCreatedAt, $returnedObject->getCreatedAt());
    }
}

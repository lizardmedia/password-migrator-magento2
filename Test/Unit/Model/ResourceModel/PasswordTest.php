<?php
declare(strict_types=1);

/**
 * File:PasswordTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\ResourceModel;

use LizardMedia\PasswordMigrator\Model\ResourceModel\Password;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PasswordTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\ResourceModel
 */
class PasswordTest extends TestCase
{
    /**
     * @var MockObject|Password
     */
    private $resourceModel;

    /**
     * @return void
     */
    protected function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceModel = $this->getMockBuilder(Password::class)
            ->setConstructorArgs(
                [
                    $context
                ]
            )
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @test
     */
    public function testCorrectTableIsInitialized()
    {
        $expectedTable = 'lm_password_migrator';

        $reflection = new \ReflectionClass(get_class($this->resourceModel));
        $field = $reflection->getProperty('_mainTable');
        $field->setAccessible(true);

        $this->assertEquals($expectedTable, $field->getValue($this->resourceModel));
    }

    /**
     * @test
     * @throws LocalizedException
     */
    public function testCorrectPrimaryKeyIsInitialized()
    {
        $expectedTable = 'id';

        $this->assertEquals($expectedTable, $this->resourceModel->getIdFieldName());
    }
}

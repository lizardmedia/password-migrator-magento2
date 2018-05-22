<?php
declare(strict_types=1);

/**
 * File:CleanupTest.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Test\Unit\Model\Config\Source;

use LizardMedia\PasswordMigrator\Model\Config\Source\Cleanup;
use PHPUnit\Framework\TestCase;

/**
 * Class CleanupTest
 * @package LizardMedia\PasswordMigrator\Test\Unit\Model\Config\Source
 */
class CleanupTest extends TestCase
{
    /**
     * @test
     */
    public function testToArray()
    {
        $source = new Cleanup();

        $expectedCount = 3;
        $expectedFirstValue = 1;
        $expectedSecondValue = 2;
        $expectedThirdValue = 0;

        $result =  $source->toOptionArray();

        $this->assertEquals($expectedCount, count($result));
        $this->assertEquals($expectedFirstValue, $result[0]['value']);
        $this->assertEquals($expectedSecondValue, $result[1]['value']);
        $this->assertEquals($expectedThirdValue, $result[2]['value']);
    }
}

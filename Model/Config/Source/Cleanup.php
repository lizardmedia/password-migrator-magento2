<?php
declare(strict_types=1);

/**
 * File:Cleanup.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Cleanup
 * @package LizardMedia\PasswordMigrator\Model\Config\Source
 */
class Cleanup implements ArrayInterface
{
    const CLEAN_YEAR = 2;
    const CLEAN_HALFYEAR = 1;
    const CLEAN_NEVER = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CLEAN_HALFYEAR,
                'label' => __('After half a year')
            ],
            [
                'value' => self::CLEAN_YEAR,
                'label' => __('After a year')
            ],
            [
                'value' => self::CLEAN_NEVER,
                'label' => __('Never')
            ]
        ];
    }
}

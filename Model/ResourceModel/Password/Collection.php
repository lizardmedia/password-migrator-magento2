<?php
declare(strict_types=1);

/**
 * File:Collection.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Model\ResourceModel\Password;

use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package LizardMedia\PasswordMigrator\Model\ResourceModel\Password
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Password::class,
            PasswordResource::class
        );
    }
}

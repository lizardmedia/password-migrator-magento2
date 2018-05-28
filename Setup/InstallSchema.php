<?php
declare(strict_types=1);

/**
 * File:InstallSchema.php
 *
 * @author Maciej Sławik <maciej.slawik@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PasswordMigrator\Setup;

use LizardMedia\PasswordMigrator\Model\Password;
use LizardMedia\PasswordMigrator\Model\ResourceModel\Password as PasswordResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package LizardMedia\PasswordMigrator\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createPasswordMigratorTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     * @throws Zend_Db_Exception
     */
    private function createPasswordMigratorTable(SchemaSetupInterface $setup): void
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(PasswordResource::TABLE_NAME)
        )->addColumn(
            Password::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            Password::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )->addColumn(
            Password::PASSWORD,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Password'
        )->addColumn(
            Password::SALT,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Salt'
        )->addColumn(
            Password::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Create date'
        )->addForeignKey(
            $setup->getFkName(
                PasswordResource::TABLE_NAME,
                Password::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id'
            ),
            Password::CUSTOMER_ID,
            $setup->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $setup->getIdxName(
                PasswordResource::TABLE_NAME,
                [
                    Password::CUSTOMER_ID
                ],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [
                Password::CUSTOMER_ID
            ],
            [
                'type' => AdapterInterface::INDEX_TYPE_UNIQUE
            ]
        )->setComment(
            'LizardMedia PasswordMigrator legacy passwords table'
        );
        $setup->getConnection()->createTable($table);
    }
}

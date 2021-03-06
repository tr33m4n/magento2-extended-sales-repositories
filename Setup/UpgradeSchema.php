<?php
namespace SnowIO\ExtendedSalesRepositories\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use SnowIO\ExtendedSalesRepositories\Api\Data\OrderRelatedDataInterface;
use SnowIO\ExtendedSalesRepositories\Model\ResourceModel;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installOrderRelatedDataSchema($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function installOrderRelatedDataSchema(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(ResourceModel\OrderRelatedData::TABLE)
        )->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'order_increment_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Increment Id'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Key for the Data'
        )->addColumn(
            'value',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Value for the Data'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Created At'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable(ResourceModel\OrderRelatedData::TABLE),
                [OrderRelatedDataInterface::ORDER_INCREMENT_ID, OrderRelatedDataInterface::CODE],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [OrderRelatedDataInterface::ORDER_INCREMENT_ID, OrderRelatedDataInterface::CODE],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )
        ->addIndex(
            $setup->getIdxName(
                $setup->getTable(ResourceModel\OrderRelatedData::TABLE),
                [OrderRelatedDataInterface::ORDER_INCREMENT_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [OrderRelatedDataInterface::ORDER_INCREMENT_ID],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )
        ->setComment(
            'Persist Key/Value information to reference to an Order'
        );

        $setup->getConnection()->createTable($table);
    }
}
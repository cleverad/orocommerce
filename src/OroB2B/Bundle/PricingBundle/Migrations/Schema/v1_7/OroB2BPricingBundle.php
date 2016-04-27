<?php

namespace OroB2B\Bundle\PricingBundle\Migrations\Schema\v1_7;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class OroB2BPricingBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createOroB2BPriceListScheduleTable($schema);
        $this->createOroB2BCplActivationRuleTable($schema);
        $this->addOrob2BPriceListScheduleForeignKeys($schema);
        $this->addOrob2BCplActivationRuleForeignKeys($schema);
        $this->alterOrob2BCmbPriceListToAccTable($schema);
        $this->alterOroB2BCmbPriceListToAccGrTable($schema);
        $this->alterOroB2BCmbPriceListToWsTable($schema);

        $this->updatePriceListChangeTriggerTable($schema);

        $queries->addPostQuery(new UpdateCPLRelationsQuery('orob2b_cmb_price_list_to_acc'));
        $queries->addPostQuery(new UpdateCPLRelationsQuery('orob2b_cmb_plist_to_acc_gr'));
        $queries->addPostQuery(new UpdateCPLRelationsQuery('orob2b_cmb_price_list_to_ws'));
        $queries->addPostQuery(new UpdateCPLNameQuery());
    }

    /**
     * Create orob2b_price_list_schedule table
     *
     * @param Schema $schema
     */
    protected function createOroB2BPriceListScheduleTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_price_list_schedule');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('price_list_id', 'integer', ['notnull' => false]);
        $table->addColumn('active_at', 'datetime', ['notnull' => false]);
        $table->addColumn('deactivate_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['price_list_id'], 'IDX_C706756E5688DED7', []);
    }

    /**
     * Create orob2b_cpl_activation_rule table
     *
     * @param Schema $schema
     */
    protected function createOroB2BCplActivationRuleTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_cpl_activation_rule');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('full_combined_price_list_id', 'integer', ['notnull' => false]);
        $table->addColumn('combined_price_list_id', 'integer', ['notnull' => false]);
        $table->addColumn('activate_at', 'datetime', ['notnull' => false]);
        $table->addColumn('expire_at', 'datetime', ['notnull' => false]);
        $table->addColumn('is_active', 'boolean', []);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create orob2b_cpl_activation_rule table
     *
     * @param Schema $schema
     */
    protected function updatePriceListChangeTriggerTable(Schema $schema)
    {
        $table = $schema->createTable('orob2b_price_list_ch_trigger');
        $table->addColumn('is_force', 'boolean', ['notnull' => false]);
    }

    /**
     * Add orob2b_price_list_schedule foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BPriceListScheduleForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_price_list_schedule');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list'),
            ['price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add orob2b_cpl_activation_rule foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrob2BCplActivationRuleForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cpl_activation_rule');
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['full_combined_price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['combined_price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Create orob2b_cmb_price_list_to_acc table
     *
     * @param Schema $schema
     */
    protected function alterOrob2BCmbPriceListToAccTable(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_price_list_to_acc');
        $table->addColumn('full_combined_price_list_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['full_combined_price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Create orob2b_cmb_plist_to_acc_gr table
     *
     * @param Schema $schema
     */
    protected function alterOroB2BCmbPriceListToAccGrTable(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_plist_to_acc_gr');
        $table->addColumn('full_combined_price_list_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['full_combined_price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Create orob2b_cmb_price_list_to_ws table
     *
     * @param Schema $schema
     */
    protected function alterOroB2BCmbPriceListToWsTable(Schema $schema)
    {
        $table = $schema->getTable('orob2b_cmb_price_list_to_ws');
        $table->addColumn('full_combined_price_list_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('orob2b_price_list_combined'),
            ['full_combined_price_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}

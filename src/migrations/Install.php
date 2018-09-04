<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\migrations;

use ournameismud\enquiries\Enquiries;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%enquiries_forms}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%enquiries_forms}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'formName' => $this->string(255)->notNull()->defaultValue(''),
                    'formIntro' => $this->text()->notNull()->defaultValue(''),
                    'formFields' => $this->text()->notNull()->defaultValue(''),
                    'formLabel' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%enquiries_notifications}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%enquiries_notifications}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'form' => $this->integer()->notNull(),
                    'recipients' => $this->string(255)->notNull()->defaultValue(''),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'message' => $this->text()->notNull()->defaultValue(''),
                    'copyFields' => $this->boolean()->defaultValue(false),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%enquiries_submissions}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%enquiries_submissions}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'form' => $this->integer()->notNull(),
                    'recipient' => $this->string(255)->notNull()->defaultValue(''),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'message' => $this->text()->notNull()->defaultValue(''),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%enquiries_messagelogs}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%enquiries_messagelogs}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'form' => $this->integer()->notNull(),
                    'recipient' => $this->string(255)->notNull()->defaultValue(''),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'message' => $this->text()->notNull()->defaultValue(''),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%enquiries_forms}}',
                'some_field',
                true
            ),
            '{{%enquiries_forms}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

        $this->createIndex(
            $this->db->getIndexName(
                '{{%enquiries_notifications}}',
                'some_field',
                true
            ),
            '{{%enquiries_notifications}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

        $this->createIndex(
            $this->db->getIndexName(
                '{{%enquiries_submissions}}',
                'some_field',
                true
            ),
            '{{%enquiries_submissions}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%enquiries_forms}}', 'siteId'),
            '{{%enquiries_forms}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%enquiries_notifications}}', 'siteId'),
            '{{%enquiries_notifications}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%enquiries_submissions}}', 'siteId'),
            '{{%enquiries_submissions}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%enquiries_forms}}');

        $this->dropTableIfExists('{{%enquiries_notifications}}');

        $this->dropTableIfExists('{{%enquiries_submissions}}');
    }
}

<?php

namespace shadowysuperstacker\nostrkit\migrations;

use Craft;
use craft\db\Migration;

/**
 * m240829_180934_create_nostr_verifications_table migration.
 */
class Install extends Migration
{
    public null|string $driver;
    private string $table_name = '{{%nostrkit_verifications}}';
    
    public function safeUp(): bool
    {
        
        $this->driver = \Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            \Craft::$app->db->schema->refresh();
        }
        
        return true;
    }
    
    protected function createTables()
    {
        $tablesCreated = false;
        
        $tableSchema = \Craft::$app->db->schema->getTableSchema($this->table_name);
        if (null === $tableSchema) {
            $tablesCreated = true;
            $this->createTable(
                $this->table_name,
                [
                    'id' => $this->primaryKey(),
                    'name' => $this->string(),
                    'npub' => $this->string(),
                    'npub_hex' => $this->string(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
        
        return $tablesCreated;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists($this->table_name);
        return true;
    }
}

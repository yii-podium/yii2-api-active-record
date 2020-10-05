<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201005113600CreatePodiumLogTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_log}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer(11)->notNull(),
            'action' => $this->string(191)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-podium_log-member_id',
            '{{%podium_log}}',
            'member_id',
            '{{%podium_member}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_log}}');

        return true;
    }
}

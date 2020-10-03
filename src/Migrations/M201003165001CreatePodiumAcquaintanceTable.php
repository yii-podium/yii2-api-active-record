<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201003165001CreatePodiumAcquaintanceTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_acquaintance}}', [
            'member_id' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'type_id' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-podium_acquaintance', '{{%podium_acquaintance}}', ['member_id', 'target_id']);
        $this->addForeignKey(
            'fk-podium_acquaintance-member_id',
            '{{%podium_acquaintance}}',
            'member_id',
            '{{%podium_member}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_acquaintance-target_id',
            '{{%podium_acquaintance}}',
            'target_id',
            '{{%podium_member}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_acquaintance}}');

        return true;
    }
}

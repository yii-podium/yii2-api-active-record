<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201025115100CreatePodiumThreadGroupTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_thread_group}}', [
            'thread_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-podium_thread_group', '{{%podium_thread_group}}', ['thread_id', 'group_id']);

        $this->addForeignKey(
            'fk-podium_thread_group-thread_id',
            '{{%podium_thread_group}}',
            'thread_id',
            '{{%podium_thread}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_thread_group-group_id',
            '{{%podium_thread_group}}',
            'group_id',
            '{{%podium_group}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_thread_group}}');

        return true;
    }
}

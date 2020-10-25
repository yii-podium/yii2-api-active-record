<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201025115000CreatePodiumForumGroupTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_forum_group}}', [
            'forum_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-podium_forum_group', '{{%podium_forum_group}}', ['forum_id', 'group_id']);

        $this->addForeignKey(
            'fk-podium_forum_group-forum_id',
            '{{%podium_forum_group}}',
            'forum_id',
            '{{%podium_forum}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_forum_group-group_id',
            '{{%podium_forum_group}}',
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
        $this->dropTable('{{%podium_forum_group}}');

        return true;
    }
}

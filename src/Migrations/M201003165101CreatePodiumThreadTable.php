<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201003165101CreatePodiumThreadTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_thread}}', [
            'id' => $this->primaryKey(),
            'forum_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'name' => $this->string(191)->notNull(),
            'slug' => $this->string(191)->notNull()->unique(),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'pinned' => $this->boolean()->notNull()->defaultValue(false),
            'locked' => $this->boolean()->notNull()->defaultValue(false),
            'posts_count' => $this->integer()->notNull()->defaultValue(0),
            'views_count' => $this->integer()->notNull()->defaultValue(0),
            'created_post_at' => $this->integer(),
            'updated_post_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'archived' => $this->boolean()->notNull()->defaultValue(false),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-podium_thread-author_id',
            '{{%podium_thread}}',
            'author_id',
            '{{%podium_member}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_thread-forum_id',
            '{{%podium_thread}}',
            'forum_id',
            '{{%podium_forum}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_thread}}');

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201003165100CreatePodiumForumTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_forum}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'name' => $this->string(191)->notNull(),
            'slug' => $this->string(191)->notNull()->unique(),
            'description' => $this->text(),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'sort' => $this->smallInteger()->notNull()->defaultValue(0),
            'threads_count' => $this->integer()->notNull()->defaultValue(0),
            'posts_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'archived' => $this->boolean()->notNull()->defaultValue(false),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-podium_forum-author_id',
            '{{%podium_forum}}',
            'author_id',
            '{{%podium_member}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_forum-category_id',
            '{{%podium_forum}}',
            'category_id',
            '{{%podium_category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_forum}}');

        return true;
    }
}

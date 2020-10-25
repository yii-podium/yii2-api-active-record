<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201025114500CreatePodiumCategoryGroupTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_category_group}}', [
            'category_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-podium_category_group', '{{%podium_category_group}}', ['category_id', 'group_id']);

        $this->addForeignKey(
            'fk-podium_category_group-category_id',
            '{{%podium_category_group}}',
            'category_id',
            '{{%podium_category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_category_group-group_id',
            '{{%podium_category_group}}',
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
        $this->dropTable('{{%podium_category_group}}');

        return true;
    }
}

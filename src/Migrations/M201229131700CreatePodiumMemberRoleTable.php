<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use yii\db\Connection;
use yii\db\Migration;

class M201229131700CreatePodiumMemberRoleTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_member_role}}', [
            'member_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
            'permit' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-podium_member_role', '{{%podium_member_role}}', ['member_id', 'role_id']);

        $this->addForeignKey(
            'fk-podium_member_role-member_id',
            '{{%podium_member_role}}',
            'member_id',
            '{{%podium_member}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_member_role-role_id',
            '{{%podium_member_role}}',
            'role_id',
            '{{%podium_role}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_member_role}}');

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use Podium\ActiveRecordApi\enums\MemberStatus;
use yii\db\Connection;
use yii\db\Migration;

class M201003165000CreatePodiumMemberTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_member}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(191)->notNull()->unique(),
            'username' => $this->string(191)->notNull()->unique(),
            'slug' => $this->string(191)->notNull()->unique(),
            'status_id' => $this->string(45)->notNull()->defaultValue(MemberStatus::REGISTERED),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        return true;
    }

    public function down(): bool
    {
        $this->dropTable('{{%podium_member}}');

        return true;
    }
}

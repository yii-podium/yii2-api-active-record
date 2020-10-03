<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Migrations;

use Podium\ActiveRecordApi\enums\MessageStatus;
use yii\db\Connection;
use yii\db\Migration;

class M201003165502CreatePodiumMessageParticipantTable extends Migration
{
    public function up(): bool
    {
        $tableOptions = null;
        /** @var Connection $db */
        $db = $this->db;
        if ('mysql' === $db->driverName) {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%podium_message_participant}}', [
            'message_id' => $this->integer(11)->notNull(),
            'member_id' => $this->integer(11)->notNull(),
            'status_id' => $this->string(45)->notNull()->defaultValue(MessageStatus::NEW),
            'side_id' => $this->string(45)->notNull(),
            'archived' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-podium_message_participant',
            '{{%podium_message_participant}}',
            ['message_id', 'member_id']
        );
        $this->addForeignKey(
            'fk-podium_message_participant-message_id',
            '{{%podium_message_participant}}',
            'message_id',
            '{{%podium_message}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-podium_message_participant-member_id',
            '{{%podium_message_participant}}',
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
        $this->dropTable('{{%podium_message_participant}}');

        return true;
    }
}
